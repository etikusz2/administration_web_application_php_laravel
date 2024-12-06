<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class UserController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', User::class);

        if (auth()->user()->role === 'admin') {
            $users = User::all();
        } elseif (auth()->user()->role === 'administrative_unit') {
            $administrativeUnitId = auth()->user()->administrativeUnitId;
            $users = User::whereHas('loginApplications', function ($query) use ($administrativeUnitId) {
                $query->whereHas('loginApprovals', function ($subQuery) use ($administrativeUnitId) {
                    $subQuery->where('administrativeUnitId', $administrativeUnitId);
                });
            })->get();
        } else {
            return back();
        }

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (auth()->user()->isAdmin()) {
            return view('users.admin-show', compact('user'));
        } elseif (auth()->user()->isAdministrativeUnit()) {
            return view('users.show', compact('user'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function validateUser($id)
    {
        $user = User::findOrFail($id);
        $currentAdminUnitId = auth()->user()->administrativeUnitId;

        foreach ($user->loginApprovals as $approval) {
            if ($approval->administrativeUnitId == $currentAdminUnitId) {
                $approval->applicationStatus = true;
                $approval->save();
            }
        }

        return redirect('/users')->with('success', 'User validation has been processed successfully!');
    }


    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect('/users')->with('status', 'User deleted successfully.');
    }
}
