<?php

namespace App\Policies;

use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ReportCommentPolicy
{
    use HandlesAuthorization;

      /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\ReportComment $reportComment
     * @return \Illuminate\Auth\Access\Response
     */
    public function delete(User $user, ReportComment $reportComment): Response
    {
        return $user->id === $reportComment->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }
}
