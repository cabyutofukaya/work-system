<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param mixed $user
     * @param array $input
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($user, array $input): void
    {

        Validator::make($input, [
            'tel' => ["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'department' => ["nullable", "string"],
        ])->validateWithBag('updateProfileInformation');



        $user->forceFill([
            'tel' => $input['tel'],
            'department' => $input['department'],
        ])->save();
    }
}
