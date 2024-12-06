<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeUnit;
use Illuminate\Http\Request;

class AdministrativeUnitController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $units = AdministrativeUnit::all();
        return response()->json($units);
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50'
        ]);

        $unit = AdministrativeUnit::create($validated);
        return response()->json($unit, 201);
    }

    // Display the specified resource
    public function show(AdministrativeUnit $unit)
    {
        return response()->json($unit);
    }

    // Update the specified resource in storage
    public function update(Request $request, AdministrativeUnit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50'
        ]);

        $unit->update($validated);
        return response()->json($unit);
    }

    // Remove the specified resource from storage
    public function destroy(AdministrativeUnit $unit)
    {
        $unit->delete();
        return response()->json(null, 204);
    }
}

