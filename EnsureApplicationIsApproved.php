<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureApplicationIsApproved
{
    public function handle(Request $request, Closure $next)
    {
        $unitId = $request->route('unitId');
        $user = Auth::user();

        $approval = $user->loginApprovals->firstWhere('administrativeUnitId', $unitId);

        // \Log::info('Approval:', ['unitId' => $unitId, 'approval' => $approval]);

        if ($approval && (int) $approval->applicationStatus === 1) {
            return $next($request);
        }

        return redirect()->route('administrative-unit.processing', ['unitId' => $unitId])
            ->with('error', 'Your application for this administrative unit is still being processed. Please wait for approval.');
    }
}
