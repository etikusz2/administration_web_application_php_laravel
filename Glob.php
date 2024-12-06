<?php

namespace App;

use DB;
use Illuminate\Support\Facades\Auth;

class Glob
{
    public static function getNotificationsCount()
    {
        $unitId = Auth::user()->administrativeUnitId;
        if ($unitId == null) // this is not an admin user
        {
            $userId = Auth::user()->id;
            $notifications = DB::table('notifications')->where('notifiable_id', $userId)->whereNull('read_at')->count();
        } else {
            // dd($unitId);
            $notifications = DB::table('notifications')->where('notifiable_id', $unitId)->whereNull('read_at')->count();
        }
        return $notifications;
    }

    public static function getNotifications()
    {
        $unitId = Auth::user()->administrativeUnitId;
        $userId = null;

        if ($unitId === null) {
            $userId = Auth::user()->id;
        }

        $notifications = DB::table('notifications')
            ->where(function ($query) use ($unitId, $userId) {
                if ($unitId !== null) {
                    $query->where('notifiable_id', $unitId);
                } elseif ($userId !== null) {
                    $query->where('notifiable_id', $userId);
                }
                $query->whereNull('read_at');
            })
            ->get();

        $notificationList = "";
        foreach ($notifications as $notification) {
            $message = json_decode($notification->data);

            if ($unitId === null && isset($notification->notifiable_id)) {
                $user = DB::table('users')->where('id', $notification->user_id)->first();
                $unitId = $user->administrativeUnitId ?? null;
            }

            if (isset($message->user_id)) {
                $url = route('notifications.user.read', $notification->id);
            } elseif (isset($message->appointment_id)) {
                $url = route('notifications.appointment.read', $notification->id);
            } elseif (isset($message->document_id)) {
                $url = route('notifications.document.read', $notification->id);
            } elseif (isset($message->upload_document_id)) {
                $url = route('notifications.documentupload.read', ['unitId' => $unitId, 'id' => $notification->id]);
            } elseif (isset($message->service_request_id)) {
                $url = route('notifications.requestdocument.read', ['unitId' => $unitId, 'id' => $notification->id]);
            } elseif (isset($message->updated_appointment_id)) {
                $url = route('notifications.updatedappointment.read', ['unitId' => $unitId, 'id' => $notification->id]);
            } elseif (isset($message->upload_announce_id)) {
                $url = route('notifications.announce.read', [$notification->id, 'unitId' => $unitId]);
            } else {
                continue;
            }
            $notificationList .= "<a href='$url' class='block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900'>{$message->message}</a>";
        }
        return $notificationList;
    }
}
