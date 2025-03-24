<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


class ResetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['email', 'required'],
            'token' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]
        ];
    }
}
