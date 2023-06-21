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
            'name_kana' => [
                "required",
                'regex:/^[ア-ン゛゜ァ-ォャ-ョー]+$/u',
            ],
            'hobby' => ["nullable"],
            'skill' => ["nullable"],
            'food' => ["nullable"],
            'trip' => ["nullable"],
            'free' => ["nullable"],
        ])->validateWithBag('updateProfileInformation');

     

        $user->forceFill([
            'name_kana' => $input['name_kana'],
            'email' => $input['email'],
            'tel' => $input['tel'],
            'department' => $input['department'],
            'hobby' => $input['hobby'],
            'skill' => $input['skill'],
            'food' => $input['food'],
            'trip' => $input['trip'],
            'free' => $input['free'],
        ])->save();
    }
}
