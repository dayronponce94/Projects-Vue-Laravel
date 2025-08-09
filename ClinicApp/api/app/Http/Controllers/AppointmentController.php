<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Notification;

class AppointmentController extends Controller
{
    public function findDoctors(Request $request)
    {
        $request->validate([
            'specialty' => 'required|string'
        ]);

        $doctors = User::where('role', 'doctor')
            ->where('specialty', $request->specialty)
            ->select('id', 'name', 'email', 'specialty')
            ->get();

        return response()->json($doctors);
    }

    public function checkAvailability(Request $request, $doctorId)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $doctor = User::findOrFail($doctorId);

        $schedules = $doctor->schedules()
            ->where('date', $request->date)
            ->where('is_available', true)
            ->get();

        return response()->json($schedules);
    }

    public function bookAppointment(Request $request)
    {
        $patient = Auth::user();

        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        if (!$schedule->is_available) {
            return response()->json(['message' => 'The selected time is no longer available'], 400);
        }

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $schedule->date . ' ' . $schedule->start_time,
            'status' => 'pending'
        ]);

        $schedule->update(['is_available' => false]);
        $doctor = User::findOrFail($request->doctor_id);

        // Create notification for patient
        Notification::create([
            'user_id' => $patient->id,
            'type' => 'appointment_booked',
            'message' => "Your appointment with Dr. {$doctor->name} has been booked!",
        ]);

        // Create notification for doctor
        Notification::create([
            'user_id' => $request->doctor_id,
            'type' => 'appointment_booked',
            'message' => "New appointment booked by {$patient->name}",
        ]);


        return response()->json($appointment, 201);
    }

    public function cancelAppointment(Appointment $appointment)
    {
        $user = Auth::user();
        if ($user->id !== $appointment->patient_id && $user->id !== $appointment->doctor_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointment->update(['status' => 'canceled']);

        $schedule = Schedule::where('doctor_id', $appointment->doctor_id)
            ->where('date', $appointment->appointment_date->format('Y-m-d'))
            ->where('start_time', $appointment->appointment_date->format('H:i'))
            ->first();

        if ($schedule) {
            $schedule->update(['is_available' => true]);
        }

        // Notification for patient
        Notification::create([
            'user_id' => $appointment->patient_id,
            'type' => 'appointment_canceled',
            'message' => "Your appointment with Dr. {$appointment->doctor->name} has been canceled.",
        ]);

        // Notification for doctor
        Notification::create([
            'user_id' => $appointment->doctor_id,
            'type' => 'appointment_canceled',
            'message' => "Appointment canceled by {$appointment->patient->name}",
        ]);

        return response()->json(['message' => 'Appointment canceled successfully']);
    }

    public function confirmAppointment(Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->id !== $appointment->doctor_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $appointment->update(['status' => 'confirmed']);

        // Notification for patient
        Notification::create([
            'user_id' => $appointment->patient_id,
            'type' => 'appointment_confirmed',
            'message' => "Your appointment with Dr. {$appointment->doctor->name} has been confirmed!",
        ]);

        return response()->json(['message' => 'Appointment confirmed successfully']);
    }

    public function patientAppointments()
    {
        /** @var \App\Models\User $patient */
        $patient = Auth::user();
        $appointments = $patient->appointmentsAsPatient()
            ->with(['doctor' => function ($query) {
                $query->select('id', 'name', 'specialty');
            }])
            ->get();

        return response()->json($appointments);
    }

    public function doctorAppointments(Request $request)
    {
        /** @var \App\Models\User $doctor */
        $doctor = Auth::user();

        $date = $request->input('date');

        $appointments = $doctor->appointmentsAsDoctor()
            ->with('patient')
            ->when($date, function ($query) use ($date) {
                return $query->whereDate('appointment_date', $date);
            })
            ->get();

        return response()->json($appointments);
    }
}
