<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockIn;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseElectronicsController extends Controller
{
    public function panel(Request $request)
    {
        return view('SuperAdmin.purchases._electronic_serials_panel');
    }

    public function store(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $items = $data['items'] ?? [];
            $total = $data['total'] ?? 0;
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $customerName = $data['customer_name'] ?? null;
            $customerPhone = $data['customer_phone'] ?? null;
            $customerEmail = $data['customer_email'] ?? null;
            $customerCompany = $data['customer_company_school'] ?? null;
            $customerFacebook = $data['customer_facebook'] ?? null;
            $customerAddress = $data['customer_address'] ?? null;
            $orderNotes = $data['order_notes'] ?? null;
            $orderStatus = $data['order_status'] ?? 'completed';
            $creditDueDate = $data['credit_due_date'] ?? null;
            $creditNotes = $data['credit_notes'] ?? null;

            DB::beginTransaction();

            // Validate each item before processing
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $branchId = $item['branch_id'];
                
                // 1. Check if product is active
                $product = Product::find($productId);
                if (!$product || $product->status !== 'active') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Product '" . ($product->product_name ? $product->product_name : 'Unknown') . "' is not active and cannot be sold.",
                    ], 422);
                }

                // 2. Check if product requires serialization
                $isSerialized = in_array($product->tracking_type, ['serial', 'imei']);
                
                if ($isSerialized) {
                    // Check if there's stock in the selected branch
                    $stockIn = StockIn::where('product_id', $productId)
                        ->where('branch_id', $branchId)
                        ->where('quantity', '>', 'sold')
                        ->first();

                    $hasStock = $stockIn && ($stockIn->quantity - $stockIn->sold) > 0;

                    foreach ($item['entries'] as $entry) {
                        $serialNumber = $entry['serial_number'] ?? '';
                        
                        // If there's stock, require serial number
                        if ($hasStock && empty(trim($serialNumber))) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Serial number is required for '" . $product->product_name . "' as it has stock in the selected branch.",
                            ], 422);
                        }
                        
                        // If no stock, serial should be empty
                        if (!$hasStock && !empty(trim($serialNumber))) {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => "Serial number should be empty for '" . $product->product_name . "' as there is no stock in the selected branch.",
                            ], 422);
                        }
                    }
                }
            }

            // Determine branch from first item
            $branchId = Auth::user()->branch_id;
            if (!empty($items)) {
                $firstItem = reset($items);
                $branchId = isset($firstItem['branch_id']) ? $firstItem['branch_id'] : $branchId;
            }

            // Create sale record
            $sale = Sale::create([
                'cashier_id' => Auth::id(),
                'employee_id' => Auth::id(),
                'branch_id' => $branchId,
                'total_amount' => $total,
                'tax' => 0,
                'payment_method' => $paymentMethod,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'customer_company_school_name' => $customerCompany,
                'customer_facebook' => $customerFacebook,
                'customer_address' => $customerAddress,
                'notes' => $orderNotes,
                'order_status' => $orderStatus,
                'credit_due_date' => $creditDueDate,
                'credit_notes' => $creditNotes,
            ]);

            // Process each item
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $branchId = $item['branch_id'];
                $unitTypeId = isset($item['unit_type_id']) ? $item['unit_type_id'] : null;
                $quantity = $item['quantity'];
                $price = $item['price'];

                // Create sale items for each entry (for serialized products)
                foreach ($item['entries'] as $entry) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'branch_id' => $branchId,
                        'unit_type_id' => $unitTypeId,
                        'quantity' => 1, // Each entry is 1 unit for electronics
                        'price' => $price,
                        'serial_number' => isset($entry['serial_number']) ? $entry['serial_number'] : null,
                        'warranty_months' => isset($entry['warranty_months']) ? $entry['warranty_months'] : 0,
                    ]);

                    // Update stock
                    $stockRecord = StockIn::where('product_id', $productId)
                        ->where('branch_id', $branchId)
                        ->where('quantity', '>', 'sold')
                        ->first();

                    if ($stockRecord) {
                        $stockRecord->increment('sold', 1);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Electronic items purchase completed successfully.',
                'sale_id' => $sale->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing electronic items purchase: ' . $e->getMessage(),
            ], 500);
        }
    }
}
