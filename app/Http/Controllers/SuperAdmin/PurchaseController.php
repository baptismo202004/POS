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
        $text = $request->input('text', '');
        $lines = explode("\n", $text);
        $matchedProducts = [];
        $unmatchedProducts = [];
        $referenceNumber = null;

        Log::info('OCR Text Received: ' . $text);
        Log::info('Lines extracted: ' . json_encode($lines));

        // Extract Reference Number
        foreach ($lines as $line) {
            if (preg_match('/(?:REFERENCE NO|REF NO|REFERENCE):\s*([A-Z0-9-]+)/i', $line, $matches)) {
                $referenceNumber = trim($matches[1]);
                break;
            }
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            Log::info('Processing line: ' . $line);
            
            // Skip common receipt headers and footers
            if (preg_match('/^(TOTAL|SUBTOTAL|CASH|CHANGE|VAT|DISCOUNT|PAYMENT|AMOUNT|QTY|PRICE|ITEM|DESCRIPTION|REF|NO|ORDER|INVOICE|RECEIPT|THANK YOU|SHOPPING AT|OFFICIAL RECEIPT)/i', $line)) {
                Log::info('Skipped receipt header/footer: ' . $line);
                continue;
            }
            
            // Skip lines that are just numbers or reference numbers
            if (preg_match('/^\d+$/', $line) || preg_match('/^[A-Z0-9-]{6,}$/', $line)) {
                Log::info('Skipped number/reference: ' . $line);
                continue;
            }
            
            // Skip lines with dates and times
            if (preg_match('/\d{1,2}\/\d{1,2}\/\d{4}|\d{1,2}:\d{2}\s*(AM|PM)/i', $line)) {
                Log::info('Skipped date/time: ' . $line);
                continue;
            }
            
            // Skip lines with addresses
            if (preg_match('/\d+\s+.*\s+(Road|St|Ave|City|Cebu)/i', $line)) {
                Log::info('Skipped address: ' . $line);
                continue;
            }
            
            // Skip lines with TIN numbers and business info
            if (preg_match('/TIN:?\s*\d+[-\d]*|BUSINESS|PERMIT|ACCREDITATION/i', $line)) {
                Log::info('Skipped business info: ' . $line);
                continue;
            }
            
            // Skip lines with special characters and symbols
            if (preg_match('/[»*©®™]/', $line)) {
                Log::info('Skipped special characters: ' . $line);
                continue;
            }
            
            // Skip lines that look like table headers
            if (preg_match('/QTY\s+ITEM\s+DESCRIPTION\s+PRICE\s+TOTAL/i', $line)) {
                Log::info('Skipped table header: ' . $line);
                continue;
            }
            
            // Skip very short lines that are likely OCR noise
            if (strlen($line) < 4) {
                Log::info('Skipped too short: ' . $line);
                continue;
            }
            
            // Skip lines that are mostly single characters or contain brackets (likely OCR errors)
            if (preg_match('/^[a-zA-Z\s]{1,4}$/', $line) || preg_match('/[\]\[\}\{\(\)]/', $line)) {
                Log::info('Skipped likely OCR error: ' . $line);
                continue;
            }
            
            // Skip lines with weird character combinations and receipt-specific noise
            if (preg_match('/^[YyNnTt]\s+[a-zA-Z]|\b[Nn][Yy]\b|\b[Yy][Tt]\b|\bEERIE\b|\bTIRE\b|\bCASHIER\b|\bSUBTOTAL\b|\bTOTAL\b|\bADMIN_USER\b|\bcn\s*&\b|k\s+a\s+CASHIER|FY|Nature\'s|\bSPRING\b/', $line)) {
                Log::info('Skipped OCR noise pattern: ' . $line);
                continue;
            }
            
            // Only process lines that start with a number (quantity) OR contain known product patterns
            // This is the key fix - most product lines on receipts start with quantity
            if (!preg_match('/^\d+\s+/', $line) && !preg_match('/\b(Lucky|Safeguard|Bear|San|Miguel|555|Sardines|Pancit|Canton)\b/i', $line)) {
                Log::info('Skipped - does not start with quantity or contain known products: ' . $line);
                continue;
            }
            
            // Try multiple regex patterns for different formats
            $matched = false;
            
            // Pattern 1: Quantity Product Name Price Total (e.g., "3 Lucky Me Pancit Canton 18.00 54.00")
            if (preg_match('/^(\d+)\s+(.+?)\s+(\d+\.\d{2})\s+(\d+\.\d{2})$/', $line, $matches)) {
                $quantity = (int)$matches[1];
                $productName = trim($matches[2]);
                $cost = (float)str_replace(',', '', $matches[3]);
                $matched = true;
                Log::info('Pattern 1 matched (Qty Name Price Total): ' . json_encode($matches));
            }
            // Pattern 2: Quantity Product Name Price (e.g., "3 Lucky Me Pancit Canton 18.00")
            elseif (preg_match('/^(\d+)\s+(.+?)\s+(\d+\.\d{2})$/', $line, $matches)) {
                $quantity = (int)$matches[1];
                $productName = trim($matches[2]);
                $cost = (float)str_replace(',', '', $matches[3]);
                $matched = true;
                Log::info('Pattern 2 matched (Qty Name Price): ' . json_encode($matches));
            }
            // Pattern 3: Product Name Price (e.g., "Lucky Me Pancit Canton 18.00")
            elseif (preg_match('/^(.+?)\s+(\d+\.\d{2})$/', $line, $matches)) {
                $productName = trim($matches[1]);
                // Only match if it looks like a product name (contains letters and is reasonable length)
                if (strlen($productName) >= 3 && strlen($productName) <= 50 && preg_match('/[a-zA-Z]/', $productName)) {
                    // Clean up product name - remove common OCR noise prefixes
                    $productName = preg_replace('/^[YyNnTt]\s+\d*\s*/', '', $productName);
                    $productName = trim($productName);
                    
                    // Only proceed if we still have a valid product name after cleaning
                    if (strlen($productName) >= 3 && preg_match('/[a-zA-Z]{2,}/', $productName)) {
                        $quantity = 1;
                        $cost = (float)str_replace(',', '', $matches[2]);
                        $matched = true;
                        Log::info('Pattern 3 matched (Name Price): ' . json_encode($matches) . ' - Cleaned: ' . $productName);
                    }
                }
            }
            // Pattern 4: Product Name with multiple words (no price) - be EXTREMELY restrictive
            // Only allow known product names without prices
            elseif (preg_match('/\b(Lucky\s+Me\s+Pancit\s+Canton|Safeguard|Bear\s+Brand|San\s+Miguel\s+Light|555\s+Sardines)\b/i', $line)) {
                $quantity = 1;
                $productName = $line;
                $cost = 0;
                $matched = true;
                Log::info('Pattern 4 matched (known product): ' . $productName);
            }
            // Pattern 5: More flexible - any line with letters that doesn't look like a total/header - DISABLED for now to avoid false positives
            // This pattern was causing too many false positives with receipt headers
            elseif (false) {
                $quantity = 1;
                $productName = $line;
                $cost = 0;
                $matched = true;
                Log::info('Pattern 5 matched (flexible): ' . $productName);
            }
            else {
                Log::info('No pattern matched for line: ' . $line);
            }
            
            if ($matched) {
                // Find the product in the database
                $product = Product::where('product_name', 'like', '%' . $productName . '%')->first();
                Log::info('Database search for: ' . $productName . ' - Found: ' . ($product ? 'Yes' : 'No'));

                if ($product) {
                    $matchedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                    Log::info('Added to matched products: ' . $product->product_name);
                } else {
                    $unmatchedProducts[] = [
                        'name' => $productName,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                    Log::info('Added to unmatched products: ' . $productName);
                }
            }
        }

        $result = [
            'reference_number' => $referenceNumber,
            'products' => $matchedProducts,
            'unmatched_products' => $unmatchedProducts
        ];
        
        Log::info('OCR Result: ' . json_encode($result));
        
        return response()->json($result);
    }
}