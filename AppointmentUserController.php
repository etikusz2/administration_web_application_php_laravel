<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\ServiceWindowSchedule;
use App\Models\ServiceWindowService;
use App\Models\AdministrativeUnit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentUserController extends Controller
{
    public function create($unitId)
    {
        $defaultService = Service::whereHas('department.administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })->first();
        $defaultService = $defaultService == null ? 1 : $defaultService->id;
        $service = request('service');

        if ($service === null || $service === '{service}') {
            $service = $defaultService;
        }
        $serviceWindowId = ServiceWindowService::where('serviceId', $service)->value('serviceWindowId');
        $week = request('week') === null ? null : request('week');
        $year = request('year') === null ? null : request('year');
        $services = Service::whereHas('department.administrativeUnit', function ($query) use ($unitId) {
            $query->where('id', $unitId);
        })->whereHas('serviceWindows', function ($query) use ($unitId) {
            $query->where('administrativeUnitId', $unitId);
        })->get();

        if (!$year || !$week) {
            $currentDate = Carbon::now();
            $year = $currentDate->year;
            $week = $currentDate->isoWeek();
        }

        $date = Carbon::now()->setISODate($year, $week);
        $startDate = $date->startOfWeek();
        $startDate->hour(8);
        $startDate->minute(0);
        $startDate->second(0);

        $dates = [$startDate];
        for ($i = 1; $i <= 6; $i++) {
            $clonedDate = $startDate->clone();
            $dates[] = $clonedDate->addDays($i);
        }
        $monthIndex = intval(Carbon::parse($startDate)->format('m'));

        $nameOfDays = ['LUN', 'MAR', 'MIE', 'JOI', 'VIN', 'SÂM', 'DUM'];
        $nameOfMonths = ['IAN', 'FEB', 'MAR', 'APR', 'MAI', 'IUN', 'IUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

        $month = $nameOfMonths[$monthIndex - 1];

        $interval = 30;
        $startHour = 8;
        $endHour = 16;
        $durationIteration = intval(($endHour - $startHour) * 60 / $interval) + 1;

        $weekSchedule = ServiceWindowSchedule::query()
            ->join('service_windows', 'service_windows.id', '=', 'service_window_schedules.serviceWindowId')
            ->join('service_window_services', 'service_windows.id', '=', 'service_window_services.serviceWindowId')
            ->whereIn('service_window_schedules.createdByUserId', function ($query) use ($unitId) {
                $query->select('id')
                    ->from('users')
                    ->where('administrativeUnitId', $unitId);
            })
            ->where('service_windows.administrativeUnitId', $unitId)
            ->where('service_window_services.serviceId', $service)
            ->where('service_window_schedules.startDateTime', '>=', Carbon::now()->addHours(3))
            ->select('service_window_schedules.*')
            ->get();

        $reservedSchedule = Appointment::query()
            ->join('services', 'appointments.serviceId', '=', 'services.id')
            ->join('service_window_services', 'services.id', '=', 'service_window_services.serviceId')
            ->join('service_windows', 'service_window_services.serviceWindowId', '=', 'service_windows.id')
            ->where('service_windows.id', $serviceWindowId)
            ->where('service_windows.administrativeUnitId', $unitId)
            ->whereRaw('str_to_date(concat(appointmentDate, " ", appointmentStartTime), "%Y-%m-%d %H:%i:%s") >= ?', [Carbon::now()->addHours(3)->toDateTimeString()])
            ->selectRaw('str_to_date(concat(appointmentDate, " ", appointmentStartTime), "%Y-%m-%d %H:%i:%s") as reservationDate')
            ->get();

        return view(
            'appointments.user.create',
            compact(
                'unitId',
                'services',
                'year',
                'week',
                'dates',
                'month',
                'nameOfDays',
                'month',
                'interval',
                'durationIteration',
                'endHour',
                'interval',
                'startHour',
                'weekSchedule',
                'service',
                'reservedSchedule',
                'defaultService'
            )
        );
    }



    public function store(Request $request, $unitId)
    {
        try {
            Log::info('Received appointment request:', $request->all());

            $validated = $request->validate([
                'serviceId' => 'required|exists:services,id',
                'appointmentDate' => 'required|date',
                'appointmentStartTime' => 'required|date_format:H:i:s',
                'appointmentEndTime' => 'required|date_format:H:i:s',
            ]);

            Log::info('Validated appointment data:', $validated);

            $appointment = new Appointment();
            $appointment->serviceId = $validated['serviceId'];
            $appointment->appointmentDate = $validated['appointmentDate'];
            $appointment->appointmentStartTime = $validated['appointmentStartTime'];
            $appointment->appointmentEndTime = $validated['appointmentEndTime'];
            $appointment->appointmentUserId = auth()->id();
            $appointment->appointmentStatus = 0;

            Log::info('Appointment before save:', $appointment->toArray());

            $appointment->save();

            $administrativeUnit = AdministrativeUnit::find($unitId);
            if ($administrativeUnit) {
                // $administrativeUnit->notify(new NewAppointmentReservation($appointment));
                $serviceName = $appointment->service->serviceName;
                $notificationData = [
                    "message" => "A fost primită o nouă programare pe serviciul {$serviceName}",
                    "appointment_id" => $appointment->id,
                    "service_id" => $appointment->serviceId
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

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error storing appointment:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error storing appointment'], 500);
        }
    }
    public function show($unitId)
    {
        $appointments = Appointment::where('appointmentUserId', auth()->user()->id)
            ->whereRaw('str_to_date(concat(appointmentDate, " ", appointmentStartTime), "%Y-%m-%d %H:%i:%s") >= NOW() + INTERVAL 3 HOUR')
            ->selectRaw('DISTINCT appointments.*, services.serviceName, service_windows.windowName, service_windows.windowLocation, str_to_date(concat(appointmentDate, " ", appointmentStartTime), "%Y-%m-%d %H:%i:%s") as reservationDate')
            ->join('services', 'appointments.serviceId', '=', 'services.id')
            ->join('service_window_services', 'services.id', '=', 'service_window_services.serviceId')
            ->join('service_windows', 'service_window_services.serviceWindowId', '=', 'service_windows.id')
            ->where('service_windows.administrativeUnitId', $unitId)
            ->with('service')
            ->get();
        return view('appointments.user.show', compact('appointments'));
    }



    public function previousWeek($unitId, $year, $week, $service)
    {
        $previousWeek = Carbon::now()->setISODate($year, $week)->subWeek();
        return redirect()->route('appointments.user.create', ['unitId' => $unitId, 'year' => $previousWeek->isoFormat('GGGG'), 'week' => $previousWeek->isoWeek(), 'service' => $service]);
    }

    public function nextWeek($unitId, $year, $week, $service)
    {
        $nextWeek = Carbon::now()->setISODate($year, $week)->addWeek();
        return redirect()->route('appointments.user.create', ['unitId' => $unitId, 'year' => $nextWeek->isoFormat('GGGG'), 'week' => $nextWeek->isoWeek(), 'service' => $service]);
    }


}
