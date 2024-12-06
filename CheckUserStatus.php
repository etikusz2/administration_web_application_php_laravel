<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->loginApprovals()->applicationStatus) {
            return redirect()->route('processing');
        }

        return $next($request);
    }
}
