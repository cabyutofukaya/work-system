<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Report $report
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Report $report): Response|bool
    {
        // 公開中であれば許可
        if (!$report->is_private) {
            return true;
        }

        // 非公開であれば自分の投稿の場合のみ許可
        return $user->id === $report->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Report $report
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Report $report): Response|bool
    {
        return $user->id === $report->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Report $report
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Report $report): Response|bool
    {
        return $user->id === $report->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }
}
