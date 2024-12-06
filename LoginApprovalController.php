<?php

namespace App\Http\Controllers;

use App\Models\LoginApproval;
use Illuminate\Http\Request;

class LoginApprovalController extends Controller
{
    public function index()
    {
        $approvals = LoginApproval::all();
        return response()->json($approvals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'loginApplicationId' => 'required|integer|exists:login_applications,id',
            'administrativeUnitId' => 'required|integer|exists:administrative_units,id',
            'applicationStatus' => 'required|string|in:pending,approved,rejected',
        ]);

        $approval = LoginApproval::create($validated);
        return response()->json($approval, 201);
    }

    public function show(LoginApproval $loginApproval)
    {
        return response()->json($loginApproval);
    }

    public function update(Request $request, LoginApproval $loginApproval)
    {
        $validated = $request->validate([
            'loginApplicationId' => 'sometimes|required|integer|exists:login_applications,id',
            'administrativeUnitId' => 'sometimes|required|integer|exists:administrative_units,id',
            'applicationStatus' => 'sometimes|required|string|in:pending,approved,rejected',
        ]);

        $loginApproval->update($validated);
        return response()->json($loginApproval);
    }

    public function destroy(LoginApproval $loginApproval)
    {
        $loginApproval->delete();
        return response()->json(null, 204);
    }
}

