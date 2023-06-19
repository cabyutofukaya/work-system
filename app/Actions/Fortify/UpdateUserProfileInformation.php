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
    public function update($user, array $input,string $file_name = ''): void
    {

        Validator::make($input, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // 論理削除済みユーザのメールアドレスを設定可能にする
                Rule::unique('users')->whereNull('deleted_at')->ignore($user->id),
            ],
            'tel' => ["nullable", 'regex:/^[0-9][-0-9]+[0-9]$/'],
            'department' => ["nullable", "string"],
            'img_file' => [
                'mimes:jpeg,jpg,webp,gif,png',
            ],
        ])->validateWithBag('updateProfileInformation');

     

        $user->forceFill([
            'email' => $input['email'],
            'tel' => $input['tel'],
            'department' => $input['department'],
            'img_file' => $file_name,
        ])->save();
    }
}
