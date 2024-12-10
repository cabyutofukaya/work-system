<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NumCheck implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $flag = false;
        if($value % 1000 == 0){
            $flag = true;
        }

        return $flag;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '数値は、1000の単位で入力してください。';
    }
}
