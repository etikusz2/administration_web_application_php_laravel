<?php

namespace App\Http\Controllers;

use App\Models\WebApplicationService;
use Illuminate\Http\Request;

class WebApplicationServiceController extends Controller
{
    public function index()
    {
        $services = WebApplicationService::with('webApplication')->get();
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'webApplicationId' => 'required|exists:web_applications,id',
            'webServiceName' => 'required|string|max:50'
        ]);

        $service = WebApplicationService::create($validated);
        return response()->json($service, 201);
    }

    public function show(WebApplicationService $service)
    {
        return response()->json($service);
    }

    public function update(Request $request, WebApplicationService $service)
    {
        $validated = $request->validate([
            'webApplicationId' => 'required|exists:web_applications,id',
            'webServiceName' => 'required|string|max:50'
        ]);

        $service->update($validated);
        return response()->json($service);
    }

    public function destroy(WebApplicationService $service)
    {
        $service->delete();
        return response()->json(null, 204);
    }
}

