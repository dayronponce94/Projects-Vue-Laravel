<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Schedule;
use App\Policies\AppointmentPolicy;
use App\Policies\SchedulePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Schedule::class => SchedulePolicy::class,
        Appointment::class => AppointmentPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Definition of Additional Gates
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
