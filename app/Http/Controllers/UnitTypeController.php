<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $request->validate([
            'unit_name' => 'required|unique:unit_types,unit_name'
        ]);

        UnitType::create(['unit_name' => $request->unit_name]);

        return back()->with('success', 'Unit type added');
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
        $request->validate([
            'unit_name' => 'required|unique:unit_types,unit_name,' . $unitType->id
        ]);

        $unitType->update(['unit_name' => $request->unit_name]);

        return redirect()->route('superadmin.unit-types.index')->with('success', 'Unit type updated successfully');
    }

    public function destroy(UnitType $unitType)
    {
        $unitType->delete();
        return back()->with('success', 'Unit type deleted successfully');
    }
}
