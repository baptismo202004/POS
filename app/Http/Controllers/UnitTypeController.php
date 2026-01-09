<?php

namespace App\Http\Controllers\SuperAdmin;

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
            'name' => 'required|unique:unit_types,name'
        ]);

        UnitType::create(['name' => $request->name]);

        return back()->with('success', 'Unit type added');
    }
}
