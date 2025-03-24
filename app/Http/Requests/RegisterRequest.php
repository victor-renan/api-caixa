<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['email', 'required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]
        ];
    }
}
