<?php

namespace App\Http\Controllers;

use App\Models\ServiceDocument;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\AdministrativeUnit;
use App\Models\Notification;
// use App\Notifications\NewRequestDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserDocumentController extends Controller
{
    public function index($unitId)
    {
        $loggedInUserId = auth()->user()->id;

        $documents = ServiceDocument::where('createdForUserId', null)
            ->orWhere('createdForUserId', $loggedInUserId)
            ->whereHas('service', function ($query) use ($unitId) {
                $query->whereHas('department.administrativeUnit', function ($query) use ($unitId) {
                    $query->where('id', $unitId);
                });
            })
            ->with('service') // Eager load the related service
            ->get()
            ->groupBy('serviceId'); // Group by service ID

        $services = Service::whereHas('department.administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })
            ->get();

        return view('documents.user.index', compact('documents', 'services', 'unitId'));
    }

    public function preview($unitId, $documentId)
    {
        $document = ServiceDocument::find($documentId);

        // Ellenőrizzük, hogy a dokumentum URL-je egy PDF fájlra mutat-e
        $extension = Str::lower(pathinfo($document->documentURL, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            // A dokumentum PDF formátumban van, így PDF előnézetet jelenítünk meg
            $pdfContent = base64_encode(Storage::get($document->documentURL));
            return view('components.preview', compact('pdfContent'));
        } else {
            // Ha a dokumentum nem PDF, akkor ne jelenítsünk meg előnézetet
            return redirect()->route('documents.user.index')->with('error', 'Preview is only available for PDF documents.');
        }
    }

    public function download($unitId, $documentId)
    {
        $document = ServiceDocument::findOrFail($documentId);
        $filePath = storage_path('app/' . $document->documentURL);
        return response()->download($filePath);
    }

    public function destroy($unitId, $documentId)
    {
        $document = ServiceDocument::findOrFail($documentId);
        $document->delete();
        return redirect()->route('documents.user.index', ['unitId' => $unitId])->with('success', 'Document has been deleted successfully.');
    }

    public function store(Request $request, $unitId)
    {
        $request->validate([
            'documentName' => 'required|string|max:50',
            'documentDescription' => 'required|string|max:150',
            'document' => 'required|file|max:10240',
            'service_id' => 'required|exists:services,id',
        ]);

        $documentPath = $request->file('document')->store('documents');

        $serviceRequest = new ServiceRequest();
        $serviceRequest->serviceId = $request->input('service_id');
        $serviceRequest->requestByUserId = auth()->id();
        $serviceRequest->requestDate = now();
        $serviceRequest->requestStatus = 0;
        $serviceRequest->requestComments = '';
        $serviceRequest->createdByUserId = auth()->id();
        $serviceRequest->createDate = now();
        $serviceRequest->save();

        $document = new ServiceDocument();
        $document->serviceId = $request->input('service_id');
        $document->documentName = $request->input('documentName');
        $document->documentDescription = $request->input('documentDescription');
        $document->documentURL = $documentPath;
        $document->requestId = $serviceRequest->id;
        $document->createdByUserId = auth()->id();
        $document->createdForUserId = $unitId;
        $document->createDate = now();
        $document->save();

        $administrativeUnit = AdministrativeUnit::find($unitId);
        if ($administrativeUnit) {
            $serviceName = $document->service->serviceName;
            $notificationData = [
                "message" => "A fost primită un document nou pe serviciul {$serviceName}",
                "document_id" => $document->id,
            ];

            $notification = new Notification();
            $notification->user_id = auth()->id();
            $notification->data = $notificationData;
            $notification->type = "App\Notifications\NewRequestDocument";
            $notification->notifiable_id = $administrativeUnit->id;
            $notification->notifiable_type = "App\Models\AdministrativeUnit";
            $notification->read_at = null;
            $notification->save();
        }

        return redirect()->route('documents.user.upload', ['unitId' => $unitId])->with('success', 'Document has been uploaded successfully.');
    }

    public function update(Request $request, $unitId, $documentId)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
        ]);

        $document = ServiceDocument::findOrFail($documentId);

        if ($document->documentURL) {
            Storage::delete($document->documentURL);
        }

        $path = $request->file('document')->store('documents');

        $document->documentURL = $path;
        $document->save();

        $document->serviceRequest->update(['updateDate' => now()]);
        return redirect()->route('documents.user.index')->with('success', 'Documentul a fost actualizat cu succes.');
    }

    public function uploadForm($unitId)
    {
        $services = Service::whereHas('department.administrativeUnit', function ($query) use ($unitId) {
            $query->where('administrativeUnitId', $unitId);
        })->get();

        return view('documents.user.uploadForm', compact('services', 'unitId'));
    }

    public function upload(Request $request, $unitId)
    {
        $documents = ServiceDocument::select('service_documents.*')
            ->join('services', 'service_documents.serviceId', '=', 'services.id')
            ->join('administrative_unit_departments', 'services.departmentId', '=', 'administrative_unit_departments.id')
            ->where('administrative_unit_departments.administrativeUnitId', $unitId)
            ->where('service_documents.createdByUserId', auth()->id())
            ->paginate(10);


        return view('documents.user.upload', compact('documents', 'unitId'));
    }
}
