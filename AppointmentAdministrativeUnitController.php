<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Notification;
use App\Models\AdministrativeUnit;
use App\Models\ServiceWindowSchedule;
use App\Models\User;
use App\Models\ServiceWindow;
use App\Models\ServiceWindowService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentAdministrativeUnitController extends Controller
{
    public function index()
    {
        $defaultServiceWindow = ServiceWindow::where('administrativeUnitId', auth()->user()->administrativeUnitId)->first();
        $defaultServiceWindow = $defaultServiceWindow == null ? 1 : $defaultServiceWindow->id;
        $currentDate = now()->toDateString();
        $currentUnitId = auth()->user()->administrativeUnitId;

        $appointments = Appointment::select('appointments.*')
            ->join('services', 'appointments.serviceId', '=', 'services.id')
            ->join('administrative_unit_departments', 'services.departmentId', '=', 'administrative_unit_departments.id')
            ->where('administrative_unit_departments.administrativeUnitId', $currentUnitId)
            ->where('appointments.appointmentDate', '>=', $currentDate)
            ->paginate(15);

        return view('appointments.admin.index', compact('appointments', 'defaultServiceWindow'));
    }

    public function show(Appointment $appointment)
    {
        $user = $appointment->user;
        $service = $appointment->service;
        $department = $service ? $service->department : null;
        $administrativeUnit = $service ? $service->administrativeUnit : null;

        return view('appointments.admin.show', compact('appointment', 'user', 'service', 'department', 'administrativeUnit'));
    }
    public function setschedule()
    {
        $defaultService = ServiceWindow::where('administrativeUnitId', auth()->user()->administrativeUnitId)->first();
        $defaultService = $defaultService == null ? 1 : $defaultService->id;
        $serviceWindow = request('serviceWindow') === null ? 1 : request('serviceWindow');
        $serviceWindowId = ServiceWindowService::where('serviceWindowId', $serviceWindow)->value('serviceWindowId');
        $week = request('week') === null ? null : request('week');
        $year = request('year') === null ? null : request('year');

        $service_windows = ServiceWindow::where('administrativeUnitId', auth()->user()->administrativeUnitId)->get();


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

        $settedTimes = ServiceWindowSchedule::query()
            ->join('service_windows', 'service_window_schedules.serviceWindowId', '=', 'service_windows.id')
            ->where('service_windows.administrativeUnitId', auth()->user()->administrativeUnitId)
            ->where('service_windows.id', $serviceWindowId)
            ->get();

        $reservedTimes = Appointment::query()
            ->join('services', 'appointments.serviceId', '=', 'services.id')
            ->join('service_window_services', 'services.id', '=', 'service_window_services.serviceId')
            ->join('service_windows', 'service_window_services.serviceWindowId', '=', 'service_windows.id')
            ->join('administrative_unit_departments', 'services.departmentId', '=', 'administrative_unit_departments.id')
            ->where('service_windows.id', $serviceWindowId)
            ->where('administrative_unit_departments.administrativeUnitId', auth()->user()->administrativeUnitId)
            ->whereRaw('str_to_date(concat(appointmentDate, " ", appointmentStartTime), "%Y-%m-%d %H:%i:%s") >= ?', [Carbon::now()->addHours(3)->toDateTimeString()])
            ->selectRaw('str_to_date(concat(appointmentDate, " ", appointmentStartTime), "%Y-%m-%d %H:%i:%s") as reservationDate')
            ->get();

        return view(
            'appointments.admin.set-schedule',
            compact(
                'service_windows',
                'serviceWindow',
                'year',
                'month',
                'week',
                'dates',
                'nameOfDays',
                'interval',
                'durationIteration',
                'endHour',
                'interval',
                'startHour',
                'settedTimes',
                'reservedTimes'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'serviceWindowId' => 'required|integer',
            'startDateTime' => 'required|date_format:Y-m-d H:i:s',
            'endDateTime' => 'required|date_format:Y-m-d H:i:s',
            'createdByUserId' => 'required|integer',
        ]);

        try {
            $schedule = new ServiceWindowSchedule();
            $schedule->serviceWindowId = $request->input('serviceWindowId');
            $schedule->startDateTime = $request->input('startDateTime');
            $schedule->endDateTime = $request->input('endDateTime');
            $schedule->createdByUserId = $request->input('createdByUserId');
            $schedule->save();

            return response()->json(['success' => true, 'message' => 'Timeslot setted available!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error occurred while saving the timeslot.']);
        }
    }

    public function previousWeek($year, $week, $serviceWindow)
    {
        $previousWeek = Carbon::now()->setISODate($year, $week)->subWeek();
        return redirect()->route('appointments.admin.set-schedule', ['year' => $previousWeek->isoFormat('GGGG'), 'week' => $previousWeek->isoWeek(), 'serviceWindow' => $serviceWindow]);
    }

    public function nextWeek($year, $week, $serviceWindow)
    {
        $nextWeek = Carbon::now()->setISODate($year, $week)->addWeek();
        return redirect()->route('appointments.admin.set-schedule', ['year' => $nextWeek->isoFormat('GGGG'), 'week' => $nextWeek->isoWeek(), 'serviceWindow' => $serviceWindow]);
    }

    public function destroy($selectedTime)
    {
        $appointment = ServiceWindowSchedule::where('startDateTime', $selectedTime)->first();

        if ($appointment) {
            $appointment->delete();

            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data nu poate fi găsită în baza de date.']);
        }
    }

    public function validate($id)
    {
        $appointment = Appointment::findOrFail($id);
        $currentAdminUnitId = auth()->user()->administrativeUnitId;

        if ($appointment->service->department->administrativeUnitId == $currentAdminUnitId) {
            $appointment->appointmentStatus = true;
            $appointment->save();

            $user = User::find($appointment->appointmentUserId);
            if ($user) {
                $administrativeUnitName = AdministrativeUnit::where('id', auth()->user()->administrativeUnitId)->value('name');
                $notificationData = [
                    "message" => "{$administrativeUnitName} a procesat starea programării",
                    "updated_appointment_id" => $appointment->id,
                    "service_id" => $appointment->serviceId
                ];
                $notification = new Notification();
                $notification->user_id = auth()->user()->id;
                $notification->data = $notificationData;
                $notification->type = "App\Notifications\AppointmentStatusUpdated";
                $notification->notifiable_type = "App\Models\User";
                $notification->notifiable_id = $user->id;
                $notification->read_at = null;
                $notification->save();
            }

            return redirect('/appointments')->with('success', 'Appoinment validation has been processed successfully!');
        }
    }
}
