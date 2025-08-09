<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Auth\Access\Response;

class SchedulePolicy
{
    public function update(User $user, Schedule $schedule)
    {
        return $user->id === $schedule->doctor_id;
    }

    public function delete(User $user, Schedule $schedule)
    {
        return $user->id === $schedule->doctor_id;
    }
}
