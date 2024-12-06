<?php

namespace App\Http\Controllers;

use App\Models\WebApplication;
use Illuminate\Http\Request;

class WebApplicationController extends Controller
{
    public function index()
    {
        $webApplications = WebApplication::all();
        return response()->json($webApplications);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'webApplicationName' => 'required|string|max:50',
            'webApplicationType' => 'required|integer'
        ]);

        $webApplication = WebApplication::create($validated);
        return response()->json($webApplication, 201);
    }

    public function show(WebApplication $webApplication)
    {
        return response()->json($webApplication);
    }

    public function update(Request $request, WebApplication $webApplication)
    {
        $validated = $request->validate([
            'webApplicationName' => 'required|string|max:50',
            'webApplicationType' => 'required|integer'
        ]);

        $webApplication->update($validated);
        return response()->json($webApplication);
    }

    public function destroy(WebApplication $webApplication)
    {
        $webApplication->delete();
        return response()->json(null, 204);
    }
}

