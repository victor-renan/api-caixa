<?php

namespace App\Models;

use App\Enums\PaymentTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'amount',
        'payment_type',
    ];

    protected $casts = [
        'payment_type' => PaymentTypes::class
    ];
}
