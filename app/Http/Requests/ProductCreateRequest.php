<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'name' => 'required',
      'description' => 'required',
      'code' => 'required|unique:products',
      'price' => 'required',
      'quantity' => 'nullable|integer',
      'image' => 'image|max:20480',
    ];
  }

  public function messages(): array
  {
    return [
      'name.required' => 'O nome é obrigatório',
      'description.required' => 'A descrição é obrigatória',
      'code.required' => 'O código é obrigatório',
      'price.required' => 'O preço é obrigatório',
      'code.unique' => 'Já existe um produto com este código',
      'quantity.integer' => 'A quantidade precisa ser um inteiro',
      'image.image' => 'Selecione uma imagem válida',
      'image.max' => 'A imagem tem limite de 20mb',
    ];
  }
}
