<?php

namespace App\Http\Requests;

use App\Enums\PaymentTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionCreateRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'amount' => 'required|numeric',
      'payment_type' => ['required', Rule::enum(PaymentTypes::class)],
    ];
  }

  public function messages(): array
  {
    return [
      'amount.required' => 'A quantia é obrigatória',
      'amount.numeric' => 'A quantia precisa ser um número',
      'payment_type.enum' => 'Tipo de pagamento inválido',
    ];
  }
}
