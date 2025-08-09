<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    public function cancel(User $user, Appointment $appointment)
    {
        return $user->id === $appointment->patient_id ||
            $user->id === $appointment->doctor_id;
    }
}
