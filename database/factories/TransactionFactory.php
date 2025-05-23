<?php

namespace Database\Factories;

use App\Enums\PaymentTypes;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => '10.0',
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
            'payment_type' => PaymentTypes::Pix,
        ];
    }
}
