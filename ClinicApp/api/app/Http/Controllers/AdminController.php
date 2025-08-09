<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboardStats()
    {
        $doctors = User::where('role', 'doctor')->count();
        $patients = User::where('role', 'patient')->count();
        $appointments = Appointment::count();

        return response()->json([
            'doctors' => $doctors,
            'patients' => $patients,
            'appointments' => $appointments
        ]);
    }

    public function specialtyStats()
    {
        $stats = Appointment::join('users as doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select('doctors.specialty', DB::raw('count(*) as count'))
            ->groupBy('doctors.specialty')
            ->get();

        return response()->json($stats);
    }

    public function doctorStats()
    {
        $stats = Appointment::join('users as doctors', 'appointments.doctor_id', '=', 'doctors.id')
            ->select(
                'doctors.id as doctor_id',
                'doctors.name as doctor_name',
                DB::raw('count(*) as count')
            )
            ->groupBy('doctors.id', 'doctors.name')
            ->get();

        return response()->json($stats);
    }



    public function getDoctors()
    {
        $doctors = User::where('role', 'doctor')->get();
        return response()->json($doctors);
    }

    public function createDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'specialty' => 'required|string'
        ]);

        $doctor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'doctor',
            'specialty' => $request->specialty
        ]);

        return response()->json($doctor, 201);
    }

    public function updateDoctor(Request $request, User $doctor)
    {
        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'User is not a doctor'], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $doctor->id,
            'specialty' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        $doctor->name = $request->name;
        $doctor->email = $request->email;
        $doctor->specialty = $request->specialty;

        if ($request->password) {
            $doctor->password = Hash::make($request->password);
        }

        $doctor->save();

        return response()->json($doctor);
    }

    public function deleteDoctor(User $doctor)
    {
        if ($doctor->role !== 'doctor') {
            return response()->json(['message' => 'User is not a doctor'], 400);
        }

        // Delete all appointments related to this doctor
        $doctor->appointmentsAsDoctor()->delete();

        // Delete all doctor's schedules
        $doctor->schedules()->delete();

        $doctor->delete();
        return response()->json(null, 204);
    }
}
