<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        // Temporarily disable middleware for testing
        // $this->middleware('ability:settings,view')->only(['index', 'show']);
        // $this->middleware('ability:purchases,edit')->only(['create', 'store', 'edit', 'update']);
        // $this->middleware('ability:settings,full')->only(['destroy']);
    }

        public function index()
    {
        $suppliers = \App\Models\Supplier::orderBy('supplier_name', 'asc')->paginate(10);
        return view('SuperAdmin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('SuperAdmin.suppliers.create');
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('=== SUPPLIER STORE METHOD STARTED ===');
        \Illuminate\Support\Facades\Log::info('Request data:', $request->all());
        
        try {
            // Convert empty strings to null
            $data = $request->all();
            foreach ($data as $key => $value) {
                if ($value === '') {
                    $data[$key] = null;
                }
            }

            \Illuminate\Support\Facades\Log::info('Processed data:', $data);

            $validator = \Illuminate\Support\Facades\Validator::make($data, [
                'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255|unique:suppliers,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::error('❌ Validation failed:', $validator->errors()->toArray());
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
                }
                
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            \Illuminate\Support\Facades\Log::info('✅ Validation passed');

            $validatedData = $validator->validated();
            $validatedData['status'] = 'active';
            
            \Illuminate\Support\Facades\Log::info('Creating supplier with data:', $validatedData);
            
            $supplier = \App\Models\Supplier::create($validatedData);
            
            \Illuminate\Support\Facades\Log::info('✅ Supplier created successfully:', [
                'supplier_id' => $supplier->id,
                'supplier' => $supplier->toArray()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'supplier' => $supplier]);
            }
            
            \Illuminate\Support\Facades\Log::info('Redirecting to index with success message');
            return redirect()->route('superadmin.suppliers.index')
                ->with('success', 'Supplier created successfully!');
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ Error creating supplier: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Exception details:', [
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => ['general' => $e->getMessage()]], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error creating supplier: ' . $e->getMessage())
                ->withInput();
        } finally {
            \Illuminate\Support\Facades\Log::info('=== SUPPLIER STORE METHOD COMPLETED ===');
        }
    }
    public function show(string $id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            return redirect()->route('superadmin.suppliers.index')->with('error', 'Supplier not found');
        }
        return view('SuperAdmin.suppliers.show', compact('supplier'));
    }
    public function edit(string $id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
        }
        return response()->json(['success' => true, 'supplier' => $supplier]);
    }

    public function update(Request $request, string $id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
            }
            return redirect()->route('superadmin.suppliers.index')->with('error', 'Supplier not found');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name,' . $id,
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $supplier->update($validator->validated());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'supplier' => $supplier]);
            }
            
            return redirect()->route('superadmin.suppliers.index')
                ->with('success', 'Supplier updated successfully!');
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating supplier: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => ['general' => $e->getMessage()]], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error updating supplier: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function destroy(Request $request, string $id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
            }
            return redirect()->route('superadmin.suppliers.index')->with('error', 'Supplier not found');
        }

        try {
            $supplierName = $supplier->supplier_name;
            $supplier->delete();
            
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Supplier deleted successfully']);
            }
            
            return redirect()->route('superadmin.suppliers.index')
                ->with('success', "Supplier '{$supplierName}' deleted successfully!");
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting supplier: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error deleting supplier'], 500);
            }
            
            return redirect()->route('superadmin.suppliers.index')
                ->with('error', 'Error deleting supplier');
        }
    }
}
