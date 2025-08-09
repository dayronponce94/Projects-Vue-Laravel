<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $doctor = Auth::user();

        $existing = Schedule::where('doctor_id', $doctor->id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })->exists();

        if ($existing) {
            return response()->json(['message' => 'You already have a schedule in that time range'], 409);
        }

        $schedule = Schedule::create([
            'doctor_id' => $doctor->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => true
        ]);

        return response()->json($schedule, 201);
    }

    public function index()
    {
        $doctor = Auth::user();
        $schedules = Schedule::where('doctor_id', $doctor->id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json($schedules);
    }

    public function update(Request $request, Schedule $schedule)
    {
        if (Auth::id() !== $schedule->doctor_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $schedule->update($request->all());
        return response()->json($schedule);
    }

    public function destroy(Schedule $schedule)
    {
        if (Auth::id() !== $schedule->doctor_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $schedule->delete();
        return response()->json(null, 204);
    }
}
