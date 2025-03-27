<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceCreateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required',
      'description' => 'required',
      'price' => 'required',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'O nome é obrigatório',
      'description.required' => 'A descrição é obrigatória',
      'price.required' => 'O preço é obrigatório',
    ];
  }
}
