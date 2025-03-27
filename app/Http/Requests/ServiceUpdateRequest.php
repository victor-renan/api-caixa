<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceUpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => ['nullable', Rule::unique('services')->ignore($this->route('id'))],
      'description' => 'nullable',
      'price' => 'nullable',
    ];
  }

  public function messages(): array
  {
    return [
      'name.unique' => 'Já existe um produto com este nome',
    ];
  }
}
