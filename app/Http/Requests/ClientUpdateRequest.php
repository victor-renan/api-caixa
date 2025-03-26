<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable',
            'phone' => ['nullable', Rule::unique('clients')->ignore($this->route('id'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório',
            'phone.required' => 'O telefone é obrigatório',
            'phone.unique' => 'Já existe um cliente com este telefone',
        ];
    }
}
