<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Meeting;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Meeting $meeting): bool
    {
        return $user->id === $meeting->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Meeting $meeting): bool
    {
        return $user->id === $meeting->user_id;
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        return $user->id === $meeting->user_id;
    }
}
