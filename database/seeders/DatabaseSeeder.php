<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::where('email', '=', 'test@mail.com')->first();

        if (!$user->exists()) {
            $user = User::factory()->create([
                'email' => 'test@mail.com',
                'password' => 'Test',
            ]);
        }

        if (!Cart::where('user_id', '=', $user->id)) {
            Cart::create(['user_id' => $user->id]);
        }
    }
}
