<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:settings,view')->only(['index', 'show']);
        $this->middleware('ability:purchases,edit')->only(['create', 'store', 'edit', 'update']);
        $this->middleware('ability:settings,full')->only(['destroy']);
    }

        public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        // Convert empty strings to null
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Supplier validation failed.', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $validatedData = $validator->validated();
            $validatedData['status'] = 'active';
            $supplier = \App\Models\Supplier::create($validatedData);
            \Illuminate\Support\Facades\Log::info('Supplier created successfully.', ['supplier_id' => $supplier->id]);
            return response()->json(['success' => true, 'supplier' => $supplier]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating supplier: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => ['general' => 'Database Error: ' . $e->getMessage()]], 500);
        }
    }
    public function show(string $id)
    {

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
            return response()->json(['success' => false, 'message' => 'Supplier not found'], 404);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name,' . $id,
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $supplier->update($validator->validated());
            return response()->json(['success' => true, 'supplier' => $supplier]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error updating supplier: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => ['general' => $e->getMessage()]], 500);
        }
    }
    public function destroy(string $id)
    {
        //
    }
}
