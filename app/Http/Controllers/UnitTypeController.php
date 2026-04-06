<?php

namespace App\Http\Controllers;

use App\Models\UnitType;
use Illuminate\Http\Request;

class UnitTypeController extends Controller
{
    public function index()
    {
        $unitTypes = UnitType::latest()->get();

        return view('SuperAdmin.unitTypes.index', compact('unitTypes'));
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'unit_name' => 'required|unique:unit_types,unit_name',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $unitType = UnitType::create(['unit_name' => $request->unit_name]);

        return response()->json(['success' => true, 'message' => 'Unit type added successfully.', 'unitType' => $unitType]);
    }

    public function create()
    {
        return view('SuperAdmin.unitTypes.create');
    }

    public function edit(UnitType $unitType)
    {
        return view('SuperAdmin.unitTypes.edit', compact('unitType'));
    }

    public function update(Request $request, UnitType $unitType)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'unit_name' => 'required|unique:unit_types,unit_name,'.$unitType->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $unitType->update(['unit_name' => $request->unit_name]);

        return response()->json(['success' => true, 'message' => 'Unit type updated successfully.', 'unitType' => $unitType->fresh()]);
    }

    public function destroy(UnitType $unitType)
    {
        $unitType->delete();

        return back()->with('success', 'Unit type deleted successfully');
    }
}
