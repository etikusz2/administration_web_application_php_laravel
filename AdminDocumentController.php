<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeUnit;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Notification;
use App\Models\ServiceDocument;
use App\Notifications\NewDocumentUploaded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDocumentController extends Controller
{
    public function index()
    {
        $currentUnitId = auth()->user()->administrativeUnitId;

        $documents = ServiceDocument::select('service_documents.*')
            ->join('services', 'service_documents.serviceId', '=', 'services.id')
            ->join('administrative_unit_departments', 'services.departmentId', '=', 'administrative_unit_departments.id')
            ->where('administrative_unit_departments.administrativeUnitId', $currentUnitId)
            ->where('service_documents.createdByUserId', auth()->user()->id)
            ->paginate(10);

        return view('documents.admin.index', compact('documents'));
    }

    public function uploadForm()
    {
        $administrativeUnitId = auth()->user()->administrativeUnitId;

        $users = User::whereHas('loginApplications.loginApprovals', function ($query) use ($administrativeUnitId) {
            $query->where('administrativeUnitId', $administrativeUnitId);
        })->with([
                    'loginApplications' => function ($query) {
                        $query->select('id', 'userId', 'firstName', 'lastName');
                    }
                ])->get();

        $users = $users->map(function ($user) {
            $user->firstName = $user->loginApplications->firstName ?? '';
            $user->lastName = $user->loginApplications->lastName ?? '';
            return $user;
        });

        $services = Service::whereHas('department.administrativeUnit', function ($query) use ($administrativeUnitId) {
            $query->where('administrativeUnitId', $administrativeUnitId);
        })->get();

        return view('documents.admin.upload', compact('users', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
            'service_id' => 'required|exists:services,id',
            'createdForUserId' => 'nullable|exists:users,id',
        ]);

        $documentPath = $request->file('document')->store('documents');

        $document = new ServiceDocument();
        $document->serviceId = $request->input('service_id');
        $document->documentName = $request->input('documentName');
        $document->documentDescription = $request->input('documentDescription');
        $document->documentURL = $documentPath;
        $document->createdByUserId = auth()->id();
        $document->createDate = now();

        if ($request->filled('createdForUserId')) {
            $document->createdForUserId = $request->input('createdForUserId');
        }

        $document->save();


        // Notify users
        if ($request->filled('createdForUserId')) {
            $user = User::find($request->input('createdForUserId'));
            if ($user) {
                // $user->notify(new NewDocumentUploaded($document));
                $administrativeUnitName = AdministrativeUnit::where('id', auth()->user()->administrativeUnitId)->value('name');
                $notificationData = [
                    "message" => "A fost primită un document nou de la unitate administrativă din {$administrativeUnitName}",
                    "upload_document_id" => $document->id,
                ];
                $notification = new Notification;
                $notification->user_id = auth()->user()->id;
                $notification->data = $notificationData;
                $notification->type = "App\Notifications\NewDocumentUploaded";
                $notification->notifiable_id = $user->id;
                $notification->notifiable_type = "App\Models\User";
                $notification->read_at = null;
                $notification->save();
            }
        } else {
            $adminUnitId = auth()->user()->administrativeUnitId;
            $users = User::whereHas('loginApplications.loginApprovals', function ($query) use ($adminUnitId) {
                $query->where('administrativeUnitId', $adminUnitId);
            })->get();

            foreach ($users as $user) {
                // $user->notify(new NewDocumentUploaded($document));
                $administrativeUnitName = AdministrativeUnit::where('id', auth()->user()->administrativeUnitId)->value('name');
                $notificationData = [
                    "message" => "A fost primită un document nou de la unitate administrativă din {$administrativeUnitName}",
                    "upload_document_id" => $document->id,
                ];
                $notification = new Notification;
                $notification->user_id = auth()->user()->id;
                $notification->data = $notificationData;
                $notification->type = "App\Notifications\NewDocumentUploaded";
                $notification->notifiable_id = $user->id;
                $notification->notifiable_type = "App\Models\User";
                $notification->read_at = null;
                $notification->save();
            }
        }

        return redirect()->route('documents.admin.index')->with('success', 'Document uploaded successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
        ]);

        $document = ServiceDocument::findOrFail($id);

        if ($document->documentURL) {
            Storage::delete($document->documentURL);
        }

        $path = $request->file('document')->store('documents');

        $document->documentURL = $path;
        $document->save();

        return redirect()->route('documents.admin.index')->with('success', 'Documentul a fost actualizat cu succes.');
    }

    public function destroy(ServiceDocument $document)
    {
        $document->delete();
        return redirect()->route('documents.admin.index')->with('success', 'Document deleted successfully');
    }

    public function preview(ServiceDocument $document)
    {
        $extension = Str::lower(pathinfo($document->documentURL, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            $pdfContent = base64_encode(Storage::get($document->documentURL));
            return view('components.preview', compact('pdfContent', 'document'));
        } else {
            return redirect()->route('documents.admin.index')->with('error', 'Preview is only available for PDF documents.');
        }
    }

    public function requestindex()
    {
        $currentUnitId = auth()->user()->administrativeUnitId;

        $documents = ServiceDocument::whereNotNull('requestId')
            ->join('service_requests', 'service_requests.id', '=', 'service_documents.requestId')
            ->join('services', 'services.id', '=', 'service_documents.serviceId')
            ->join('administrative_unit_departments', 'administrative_unit_departments.id', '=', 'services.departmentId')
            ->join('users', 'service_documents.createdByUserId', '=', 'users.id')
            ->join('login_applications', 'login_applications.userId', '=', 'users.id')
            ->where('administrative_unit_departments.administrativeUnitId', $currentUnitId)
            ->where('service_documents.createdForUserId', $currentUnitId)
            ->select(
                'service_documents.*',
                'login_applications.firstName',
                'login_applications.lastName',
                'login_applications.personalIdentificationNumber',
                'services.serviceName',
                'service_requests.id as request_id',
                'service_requests.requestDate',
                'service_requests.requestStatus',
                'service_requests.requestComments'
            )
            ->orderBy('service_documents.id', 'desc')
            ->paginate(10);

        return view('documents.admin.requestindex', compact('documents'));
    }

    public function download(ServiceDocument $document)
    {
        $filePath = storage_path('app/' . $document->documentURL);
        return response()->download($filePath);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'requestStatus' => 'required|integer|in:1,2',
            'requestComments' => 'required|string|max:255',
        ]);
        $document = ServiceDocument::findOrFail($id);
        $serviceRequest = ServiceRequest::findOrFail($document->requestId);
        $serviceRequest->requestStatus = $request->requestStatus;
        $serviceRequest->requestComments = $request->requestComments;
        $serviceRequest->save();

        // Notify user
        $user = User::find($serviceRequest->requestByUserId);
        if ($user) {
            // $user->notify(new DocumentStatusUpdated($serviceRequest));
            $administrativeUnitName = AdministrativeUnit::where('id', auth()->user()->administrativeUnitId)->value('name');
            $notificationData = [
                "message" => "{$administrativeUnitName} a modificat starea cererii",
                "service_request_id" => $document->id,
            ];
            $notification = new Notification();
            $notification->user_id = auth()->user()->id;
            $notification->data = $notificationData;
            $notification->type = "App\Notifications\DocumentStatusUpdated";
            $notification->notifiable_type = "App\Models\User";
            $notification->notifiable_id = $document->createdByUserId;
            $notification->read_at = null;
            $notification->save();
        }
        return response()->json(['succes' => true]);
    }
}

