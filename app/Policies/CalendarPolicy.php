<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Calendar;

class TodoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Calendar $calendar): bool
    {
        return $user->id === $calendar->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Calendar $calendar): bool
    {
        return $user->id === $calendar->user_id;
    }

    public function delete(User $user, Calendar $calendar): bool
    {
        return $user->id === $calendar->user_id;
    }
}
