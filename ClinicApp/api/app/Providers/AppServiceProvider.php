<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Schedule;
use App\Models\Appointment;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('update-schedule', function ($user, Schedule $schedule) {
            return $user->id === $schedule->doctor_id;
        });

        Gate::define('delete-schedule', function ($user, Schedule $schedule) {
            return $user->id === $schedule->doctor_id;
        });

        Gate::define('cancel-appointment', function ($user, Appointment $appointment) {
            return $user->id === $appointment->patient_id ||
                $user->id === $appointment->doctor_id;
        });
    }
}
