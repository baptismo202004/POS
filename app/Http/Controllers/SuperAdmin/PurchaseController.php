<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\UnitType;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
   
    public function index()
    {
        $purchases = Purchase::with(['items.product'])
            ->latest('purchase_date')
            ->paginate(15);

        return view('SuperAdmin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        $product_types = ProductType::all();
        $unit_types = UnitType::all();
        $suppliers = Supplier::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();

        return view('SuperAdmin.purchases.create', compact('products', 'brands', 'categories', 'product_types', 'unit_types', 'suppliers', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.is_new' => 'nullable|boolean',
            'items.*.product_id' => 'required_if:items.*.is_new,null|exists:products,id',
            'items.*.product_name' => 'required_if:items.*.is_new,1|string|max:255|unique:products,product_name',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_type_id' => 'required|exists:unit_types,id',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $totalCost = 0;
            $purchaseItemsData = [];

            foreach ($validated['items'] as $item) {
                $productId = null;
                if (!empty($item['is_new'])) {
                    $newProduct = Product::create([
                        'product_name' => $item['product_name'],
                        'barcode' => 'BC-' . uniqid(),
                        'status' => 'active',
                        'tracking_type' => 'none',
                        'warranty_type' => 'none',
                    ]);
                    $newProduct->unitTypes()->sync([$item['unit_type_id']]);
                    $productId = $newProduct->id;
                } else {
                    $productId = $item['product_id'];
                }

                $subtotal = $item['quantity'] * $item['cost'];
                $totalCost += $subtotal;

                $purchaseItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'unit_type_id' => $item['unit_type_id'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $subtotal,
                ];
            }

            $purchase = Purchase::create([
                'purchase_date' => $validated['purchase_date'],
                'supplier_id' => $validated['supplier_id'],
                'total_cost' => $totalCost,
                'payment_status' => $validated['payment_status'],
                'reference_number' => $validated['reference_number'] ?? null,
            ]);

            $purchase->items()->createMany($purchaseItemsData);
        });

        return redirect()->route('superadmin.purchases.index')->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'items.unitType');
        return view('SuperAdmin.purchases.show', compact('purchase'));
    }

    public function matchProduct(Request $request)
    {
        try {
            $text = (string) $request->input('text', '');
            $lines = explode("\n", $text);
            $matchedProducts = [];
            $unmatchedProducts = [];
            $referenceNumber = null;
            $aggregated = [];

            Log::info('OCR request received', [
                'text_length' => strlen($text),
                'lines_count' => count($lines),
            ]);

            foreach ($lines as $line) {
                if (preg_match('/(?:REFERENCE NO|REF NO|REFERENCE):\s*([A-Z0-9-]+)/i', $line, $matches)) {
                    $referenceNumber = trim($matches[1]);
                    break;
                }
            }

            $normalizeName = function (string $name): string {
                $name = preg_replace('/[^A-Za-z0-9\s\/\-\+\&\.]/', ' ', $name);
                $name = preg_replace('/\s+/', ' ', $name);
                $name = trim($name);
                $name = preg_replace('/^(BY\s+|ALT\s+GR\s+|HOME\s+)/i', '', $name);
                $name = trim($name);

                // Never treat totals/summary lines as products
                $lettersOnly = strtoupper(preg_replace('/[^A-Za-z]/', '', $name));
                if (
                    preg_match('/\b(SUBTOTAL|TOTAL|VAT|TAX|CHANGE|CASH|DISCOUNT|AMOUNT\s+DUE|GRAND\s+TOTAL)\b/i', $name)
                    || preg_match('/^(SUBTOTAL|SUBTOT|SUBTOTL|SUBOTAL|SUBOTAI|SUBOTAl|SUBOTL|SUBTOAL)$/i', $lettersOnly)
                    || str_contains($lettersOnly, 'SUBTOTAL')
                    || str_contains($lettersOnly, 'GRANDTOTAL')
                ) {
                    return '';
                }

                return $name;
            };

            $normalizeForCompare = function (string $s): string {
                $s = strtoupper($s);
                $s = preg_replace('/[^A-Z0-9]/', '', $s);
                return $s ?? '';
            };

            $findBestProductMatch = function (string $ocrName, float $minScore = 70.0) use ($normalizeForCompare) {
                $ocrName = trim($ocrName);
                if ($ocrName === '') {
                    return null;
                }

                $tokens = preg_split('/\s+/', strtoupper(preg_replace('/[^A-Za-z0-9\s]/', ' ', $ocrName))) ?: [];
                $tokens = array_values(array_filter($tokens, function ($t) {
                    return strlen($t) >= 3;
                }));
                $tokens = array_slice($tokens, 0, 6);

                if (empty($tokens)) {
                    return null;
                }

                $candidates = Product::query()
                    ->select(['id', 'product_name'])
                    ->when(\Schema::hasColumn('products', 'status'), function ($q) {
                        $q->where('status', 'active');
                    })
                    ->where(function ($q) use ($tokens) {
                        foreach ($tokens as $t) {
                            $q->orWhere('product_name', 'like', '%' . $t . '%');
                        }
                    })
                    ->limit(80)
                    ->get();

                if ($candidates->isEmpty()) {
                    return null;
                }

                $ocrNorm = $normalizeForCompare($ocrName);
                if ($ocrNorm === '') {
                    return null;
                }

                $best = null;
                $bestScore = 0.0;

                foreach ($candidates as $candidate) {
                    $candNorm = $normalizeForCompare((string) $candidate->product_name);
                    if ($candNorm === '') {
                        continue;
                    }

                    $percent = 0.0;
                    similar_text($ocrNorm, $candNorm, $percent);

                    if ($percent > $bestScore) {
                        $bestScore = $percent;
                        $best = $candidate;
                    }
                }

                // Accept match only if similarity is strong enough
                if ($best && $bestScore >= $minScore) {
                    return $best;
                }

                return null;
            };

            $inItems = false;
            $currentDesc = null;
            $debug = [
                'price_lines_matched' => 0,
                'desc_lines_seen' => 0,
                'items_aggregated' => 0,
                'fallback_used' => false,
                'desc_only_fuzzy_added' => 0,
            ];

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                // Enter item section when we see the typical table header
                if (preg_match('/^DESC\s+QTY\b/i', $line)) {
                    $inItems = true;
                    $currentDesc = null;
                    continue;
                }

                // Stop when totals start (only once we are already in items)
                if ($inItems && preg_match('/^(TOTAL|SUBTOTAL|VAT|DISCOUNT|CASH|CHANGE)\b/i', $line)) {
                    break;
                }

                // Skip obvious non-item lines
                if (preg_match('/^(TOTAL|SUBTOTAL|CASH|CHANGE|VAT|DISCOUNT|PAYMENT|AMOUNT|QTY|PRICE|ITEM|DESCRIPTION|REF|NO|ORDER|INVOICE|RECEIPT|THANK YOU|SHOPPING AT|OFFICIAL RECEIPT|X|TAX|SERVICE|CHARGE|DRIVER|HELLO|GROSS|LESS)$/i', $line)) {
                    continue;
                }
                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $line) || preg_match('/^\d{1,2}:\d{2}\s*(AM|PM)$/', $line)) {
                    continue;
                }
                if (preg_match('/^\d+(\.\d{2})?$/', $line)) {
                    continue;
                }

                // Receipt line format (common): QTY UNITPRICE AMOUNT + optional letters (V/NV)
                // Be tolerant to OCR variations: allow comma separators, 1-2 decimals, and no space before trailing letters.
                if (preg_match('/^(\d{1,3})\s+([0-9,]+(?:\.[0-9]{1,2})?)\s+([0-9,]+(?:\.[0-9]{1,2})?)(?:\s*[A-Z]{1,4})?$/', $line, $m)) {
                    $qty = (int) $m[1];
                    $unit = (float) str_replace(',', '', $m[2]);
                    $debug['price_lines_matched']++;

                    // If OCR missed the DESC/QTY header, assume we are in items once we see a price line
                    $inItems = true;

                    if ($currentDesc !== null) {
                        $name = $normalizeName($currentDesc);
                        if ($name !== '') {
                            $key = strtoupper($name);
                            if (!isset($aggregated[$key])) {
                                $aggregated[$key] = [
                                    'name' => $name,
                                    'quantity' => 0,
                                    'cost' => $unit,
                                ];
                            }
                            $aggregated[$key]['quantity'] += $qty;
                            // keep first unit cost (or replace if 0)
                            if (empty($aggregated[$key]['cost'])) {
                                $aggregated[$key]['cost'] = $unit;
                            }
                        }
                        $currentDesc = null;
                    }
                    continue;
                }

                // Collect description lines only after we've entered item section
                if ($inItems) {
                    // Description line should contain letters
                    if (preg_match('/[A-Za-z]/', $line) && strlen($line) >= 3 && strlen($line) <= 80) {
                        $debug['desc_lines_seen']++;
                        $clean = $normalizeName($line);
                        if ($clean === '') {
                            continue;
                        }

                        // If a previous description exists and this looks like continuation, append
                        if ($currentDesc !== null && !preg_match('/\b\d+\.\d{2}\b/', $clean)) {
                            $currentDesc .= ' ' . $clean;
                            $currentDesc = trim($currentDesc);
                        } else {
                            $currentDesc = $clean;
                        }

                        // Secondary recovery: if OCR misses the price-line, try to map desc lines directly to an existing product.
                        // Use stricter similarity threshold to avoid adding noise.
                        $best = $findBestProductMatch($currentDesc, 82.0);
                        if ($best) {
                            $key = strtoupper($normalizeName((string) $best->product_name));
                            if ($key !== '' && !isset($aggregated[$key])) {
                                $aggregated[$key] = [
                                    'name' => (string) $best->product_name,
                                    'quantity' => 1,
                                    'cost' => 0,
                                ];
                                $debug['desc_only_fuzzy_added']++;
                            }
                        }
                    }
                    continue;
                }
            }

            // Fallback parsing for other receipt formats (when we couldn't aggregate anything)
            if (empty($aggregated)) {
                $debug['fallback_used'] = true;

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '') {
                        continue;
                    }

                    $matched = false;
                    $quantity = 1;
                    $cost = 0;
                    $productName = '';

                    if (preg_match('/^(TOTAL|SUBTOTAL|CASH|CHANGE|VAT|DISCOUNT|PAYMENT|AMOUNT|QTY|PRICE|ITEM|DESCRIPTION|REF|NO|ORDER|INVOICE|RECEIPT|THANK YOU|SHOPPING AT|OFFICIAL RECEIPT|X|TAX|SERVICE|CHARGE|DRIVER|HELLO|GROSS|LESS)$/i', $line)) {
                        continue;
                    }
                    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $line) || preg_match('/^\d{1,2}:\d{2}\s*(AM|PM)$/', $line)) {
                        continue;
                    }
                    if (preg_match('/^\d+(\.\d{2})?$/', $line)) {
                        continue;
                    }
                    if (strlen($line) < 3) {
                        continue;
                    }

                    // Fallback 4: Description-only lines (safe) - must look like a real product (has unit/packaging)
                    // Examples: "TANDUAY 5 YEARS 750ml/12", "SUPER Q GOLDEN BHON 227g/60"
                    if (!$matched) {
                        $candidate = $normalizeName($line);
                        if (
                            $candidate !== ''
                            && preg_match('/[A-Za-z]/', $candidate)
                            && strlen($candidate) >= 5
                            && strlen($candidate) <= 80
                            && preg_match('/\b(\d+\s*(ML|L|G)\b|\d+\s*(ml|l|g)\b|\d+\s*YEARS\b|\d+\s*\+\s*\d+|\d+\s*\/\s*\d+|\d+\s*\/\d+)/i', $candidate)
                        ) {
                            $productName = $candidate;
                            $quantity = 1;
                            $cost = 0;
                            $matched = true;
                        }
                    }

                    if (preg_match('/^(\d+)\s+(.+?)\s+(\d+\.\d{2})\s+(\d+\.\d{2})$/', $line, $matches)) {
                        $quantity = (int) $matches[1];
                        $productName = $normalizeName((string) $matches[2]);
                        $cost = (float) str_replace(',', '', $matches[3]);
                        $matched = $productName !== '';
                    } elseif (preg_match('/^(\d+)\s+(.+?)\s+(\d+\.\d{2})$/', $line, $matches)) {
                        $quantity = (int) $matches[1];
                        $productName = $normalizeName((string) $matches[2]);
                        $cost = (float) str_replace(',', '', $matches[3]);
                        $matched = $productName !== '';
                    } elseif (preg_match('/^(.+?)\s+(\d+\.\d{2})$/', $line, $matches)) {
                        $productName = $normalizeName((string) $matches[1]);
                        if ($productName !== '' && preg_match('/[A-Za-z]/', $productName)) {
                            $quantity = 1;
                            $cost = (float) str_replace(',', '', $matches[2]);
                            $matched = true;
                        }
                    }

                    if (!$matched || $productName === '') {
                        continue;
                    }

                    $key = strtoupper($productName);
                    if (!isset($aggregated[$key])) {
                        $aggregated[$key] = [
                            'name' => $productName,
                            'quantity' => 0,
                            'cost' => $cost,
                        ];
                    }
                    $aggregated[$key]['quantity'] += $quantity;
                    if (empty($aggregated[$key]['cost']) && $cost > 0) {
                        $aggregated[$key]['cost'] = $cost;
                    }
                }
            }

            foreach ($aggregated as $row) {
                $productName = $row['name'];
                $quantity = (int) ($row['quantity'] ?? 1);
                $cost = (float) ($row['cost'] ?? 0);

                $product = Product::where('product_name', 'like', '%' . $productName . '%')->first();
                if (!$product) {
                    $product = $findBestProductMatch($productName);
                }
                if ($product) {
                    $matchedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                } else {
                    $unmatchedProducts[] = [
                        'name' => $productName,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                }
            }

            $debug['items_aggregated'] = count($aggregated);

            $payload = [
                'reference_number' => $referenceNumber,
                'products' => $matchedProducts,
                'unmatched_products' => $unmatchedProducts,
            ];

            if (config('app.debug')) {
                $payload['debug'] = $debug;
            }

            return response()->json($payload);
        } catch (\Throwable $e) {
            Log::error('OCR matchProduct failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $payload = [
                'success' => false,
                'message' => 'OCR processing failed',
            ];

            if (config('app.debug')) {
                $payload['error'] = $e->getMessage();
                $payload['error_file'] = $e->getFile();
                $payload['error_line'] = $e->getLine();
            }

            return response()->json($payload, 500);
        }
    }
}