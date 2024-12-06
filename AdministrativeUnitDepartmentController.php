<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeUnitDepartment;
use Illuminate\Http\Request;

class AdministrativeUnitDepartmentController extends Controller
{
    public function index()
    {
        $departments = AdministrativeUnitDepartment::all();
        return response()->json($departments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'administrativeUnitId' => 'required|exists:administrative_units,id',
            'departmentName' => 'required|string|max:80',
            'webApplicationId' => 'exists:web_applications,id',
            'webApplicationURL' => 'string|max:150'
        ]);

        $department = AdministrativeUnitDepartment::create($validated);
        return response()->json($department, 201);
    }

    public function show(AdministrativeUnitDepartment $department)
    {
        return response()->json($department);
    }

    public function update(Request $request, AdministrativeUnitDepartment $department)
    {
        $validated = $request->validate([
            'administrativeUnitId' => 'required|exists:administrative_units,id',
            'departmentName' => 'required|string|max:80',
            'webApplicationId' => 'exists:web_applications,id',
            'webApplicationURL' => 'string|max:150'
        ]);

        $department->update($validated);
        return response()->json($department);
    }

    public function destroy(AdministrativeUnitDepartment $department)
    {
        $department->delete();
        return response()->json(null, 204);
    }
}

