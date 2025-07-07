<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'nullable',
      'description' => 'nullable',
      'code' => ['nullable', Rule::unique('products')->ignore($this->id)],
      'price' => 'nullable',
      'quantity' => 'nullable|integer',
      'image' => 'nullable|image|max:20480',
    ];
  }

  public function messages(): array
  {
    return [
      'code.unique' => 'Já existe um produto com este código',
      'quantity.integer' => 'A quantidade precisa ser um inteiro',
      'image.image' => 'Selecione uma imagem válida',
      'image.max' => 'A imagem tem limite de 20mb',
    ];
  }
}
