<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdministrativeUnitDepartment;
use App\Models\WebApplication;
use App\Models\ServiceWindow;
use App\Models\Service;

class AdminUnitServicesController extends Controller
{
    public function index()
    {
        $currentUnitId = auth()->user()->administrativeUnitId;
        $departments = AdministrativeUnitDepartment::with('services')
            ->where('administrativeUnitId', $currentUnitId)
            ->get();

        $serviceWindows = ServiceWindow::where('administrativeUnitId', $currentUnitId)->with('services')
            ->get();


        return view('services.index', compact('departments', 'serviceWindows'));
    }

    public function createDepartment()
    {
        $webApplications = WebApplication::all();
        return view('services.createDepartment', compact('webApplications'));
    }

    public function storeDepartment(Request $request)
    {
        $currentUnitId = auth()->user()->administrativeUnitId;

        $validated = $request->validate([
            'departmentName' => 'required|string|max:50',
            'webApplicationId' => 'nullable|exists:web_applications,id',
            'webApplicationURL' => 'nullable|string|max:255'
        ]);

        $validated['administrativeUnitId'] = $currentUnitId;

        $department = AdministrativeUnitDepartment::create($validated);
        return redirect()->route('services.index')->with('success', 'A részleg sikeresen létrehozva.');
    }

    public function editDepartment($deparmentId)
    {
        $department = AdministrativeUnitDepartment::find($deparmentId);
        $webApplications = WebApplication::all();
        return view('services.editDepartment', compact('department', 'webApplications'));
    }

    public function updateDepartment(Request $request, $deparmentId)
    {
        $department = AdministrativeUnitDepartment::find($deparmentId);

        $validated = $request->validate([
            'departmentName' => 'required|string|max:50',
            'webApplicationId' => 'nullable|exists:web_applications,id',
            'webApplicationURL' => 'nullable|string|max:255'
        ]);

        $department->update($validated);
        return redirect()->route('services.index')->with('success', 'Departamentul actualizat cu succes.');
    }

    public function destroyDepartment($deparmentId)
    {
        $department = AdministrativeUnitDepartment::find($deparmentId);
        if ($department) {
            $department->delete();
            return redirect()->route('services.index')->with('success', 'Deparmentul șters cu succes.');
        }
        return back()->with('error', 'Departamentul nu a fost găsit.');
    }

    public function createService($departmentId)
    {
        $department = AdministrativeUnitDepartment::find($departmentId);
        return view('services.createService', compact('department'));
    }

    public function storeService(Request $request, $departmentId)
    {
        $department = AdministrativeUnitDepartment::find($departmentId);

        $validated = $request->validate([
            'serviceName' => 'required|string|max:50',
            'serviceDescription' => 'required|string|max:255'
        ]);

        $validated['departmentId'] = $departmentId;

        $service = $department->services()->create($validated);
        return redirect()->route('services.index')->with('success', 'Serviciu creat cu succes.');
    }

    public function updateService(Request $request, $id, $departmentId)
    {
        $service = Service::where('id', $id)->where('departmentId', $departmentId)->first();

        if (!$service) {
            return redirect()->route('services.index')->with('error', 'A szolgáltatás nem található.');
        }

        $validated = $request->validate([
            'serviceName' => 'required|string|max:50',
            'serviceDescription' => 'required|string|max:255'
        ]);

        $service->update($validated);

        return redirect()->route('services.index')->with('success', 'A szolgáltatás sikeresen frissítve.');
    }

    public function destroyService($id, $departmentId)
    {
        $service = Service::where('id', $id)->where('departmentId', $departmentId)->first();
        if ($service) {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Serviciu șters cu succes.');
        }
        return back()->with('error', 'Serviciu nu a fost găsit.');
    }

    public function editService($departmentId, $serviceId)
    {
        $temp = $departmentId;
        $departmentId = $serviceId;
        $serviceId = $temp;

        $service = Service::where('departmentId', $departmentId)->where('id', $serviceId)->first();

        if (!$service) {
            return redirect()->back()->with('error', 'Serviciul nu se găsește în această secțiune.');
        }

        $department = AdministrativeUnitDepartment::find($departmentId);
        return view('services.editService', compact('service', 'department'));
    }

    public function createServiceWindow()
    {
        $unitId = auth()->user()->administrativeUnitId;
        $services = Service::whereHas('department.administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })->get();
        return view('services.createServiceWindow', compact('services'));
    }

    public function storeServiceWindow(Request $request)
    {
        $currentUnitId = auth()->user()->administrativeUnitId;

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'services' => 'array',
        ]);

        $validated['administrativeUnitId'] = $currentUnitId;

        $serviceWindow = ServiceWindow::create([
            'windowName' => $validated['name'],
            'windowLocation' => $validated['location'],
            'administrativeUnitId' => $currentUnitId
        ]);

        if (isset($validated['services']) && is_array($validated['services'])) {
            foreach ($validated['services'] as $serviceId) {
                $serviceWindow->services()->attach($serviceId);
            }
        }

        return redirect()->route('services.index')->with('success', 'Ghișeu creat cu succes.');
    }

    public function editServiceWindow($id)
    {
        $unitId = auth()->user()->administrativeUnitId;
        $services = Service::whereHas('department.administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })->get();

        $serviceWindow = ServiceWindow::find($id);
        return view('services.editServiceWindow', compact('serviceWindow', 'services'));
    }

    public function updateServiceWindow(Request $request, $id)
    {
        $serviceWindow = ServiceWindow::find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'location' => 'nullable|string|max:255',
            'services' => 'array',
        ]);

        $serviceWindow->update([
            'windowName' => $validated['name'],
            'windowLocation' => $validated['location']
        ]);

        $serviceWindow->services()->detach();

        if (isset($validated['services']) && is_array($validated['services'])) {
            foreach ($validated['services'] as $serviceId) {
                $serviceWindow->services()->attach($serviceId);
            }
        }

        return redirect()->route('services.index')->with('success', 'Ghișeu actualizat cu succes.');
    }


    public function destroyServiceWindow($id)
    {
        $serviceWindow = ServiceWindow::find($id);
        if ($serviceWindow) {
            $serviceWindow->delete();
            return redirect()->route('services.index')->with('success', 'Ghișeu șters cu succes.');
        }
        return back()->with('error', 'Ghișeul nu a fost găsit.');
    }
}
