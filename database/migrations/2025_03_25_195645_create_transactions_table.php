<?php

use App\Enums\PaymentTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('client_id')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->decimal('amount')->nullable();
            $table->enum('payment_type', [
                PaymentTypes::Card->value,
                PaymentTypes::Money->value,
                PaymentTypes::Pix->value,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
