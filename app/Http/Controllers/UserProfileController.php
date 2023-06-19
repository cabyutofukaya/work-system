<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Response;
use Inertia\ResponseFactory;

class UserProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response|\Inertia\ResponseFactory
     */
    public function edit(): Response|ResponseFactory
    {
        return inertia('UserProfileEdit', [
            'user' => User::find(auth()->id())->only(["email", "tel", "department","img_file"]),
        ]);
    }
}
