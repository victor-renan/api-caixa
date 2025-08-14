<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required',
            'quantity' => 'required|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O id do produto é obrigatório',
            'quantity.required' => 'A quantidade é obrigatória',
            'quantity.min' => 'A quantidade precisa ser pelo menos 1',
        ];
    }
}
