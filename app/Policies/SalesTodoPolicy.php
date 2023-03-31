<?php

namespace App\Policies;

use App\Models\SalesTodo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SalesTodoPolicy
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
     * @param \App\Models\SalesTodo $salesTodo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, SalesTodo $salesTodo): Response|bool
    {
        return true;
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
     * @param \App\Models\SalesTodo $salesTodo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, SalesTodo $salesTodo): Response|bool
    {
        return $user->id === $salesTodo->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\SalesTodo $salesTodo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, SalesTodo $salesTodo): Response|bool
    {
        return $user->id === $salesTodo->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }

    /**
     * 対応済みフラグトグル
     *
     * @param \App\Models\User $user
     * @param \App\Models\SalesTodo $salesTodo
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function complete(User $user, SalesTodo $salesTodo): Response|bool
    {
        return $user->id === $salesTodo->user_id
            ? Response::allow()
            : Response::deny('所有していないレコードへのアクセス');
    }
}
