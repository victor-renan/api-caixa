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
        $product = Product::latest()->first();
        
        return [
            'role_id' => $product->id,
            'role_type' => Product::class,
            'amount' => $product->price,
            'client_id' => Client::latest()->first()->id,
            'user_id' => User::latest()->first()->id,
            'payment_type' => array_rand(PaymentTypes::cases(), 1),
        ];
    }
}
