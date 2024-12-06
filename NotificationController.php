<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function readUserNotification($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['user_id'])) {
                return redirect()->route('users.show', ['user' => $data['user_id']]);
            }
        }
        return redirect()->back();
    }

    public function readAppointmentNotification($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['appointment_id'])) {
                return redirect()->route('admin.appointments.show', ['appointment' => $data['appointment_id']]);
            }
        }
        return redirect()->back();
    }

    public function readDocumentNotification($id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['document_id'])) {
                return redirect()->route('documents.admin.requestindex');
            }
        }
        return redirect()->back();
    }

    public function readUpdateDocumentStatus($unitId, $id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['service_request_id'])) {
                return redirect()->route('documents.user.upload', ['unitId' => request()->segment(1)]);
            }
        }
        return redirect()->back();
    }

    public function readNewDocumentUploaded($unitId, $id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['upload_document_id'])) {
                return redirect()->route('documents.user.index', ['unitId' => $unitId]);
            }
        }
        return redirect()->back();
    }

    public function readUpdateAppointmentStatus($unitId, $id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['updated_appointment_id'])) {
                return redirect()->route('appointments.user.show', ['id' => $data['updated_appointment_id'], 'unitId' => $unitId, 'service' => $data['service_id']]);
            }
        }
        return redirect()->back();
    }

    public function readNewAnnounceUpload($unitId, $id)
    {
        $notification = DatabaseNotification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;
            if (isset($data['upload_announce_id'])) {
                return redirect()->route('announces.show', ['unitId' => $unitId, 'announce' => $data['upload_announce_id']]);
            }
        }
        return redirect()->back();
    }
}
