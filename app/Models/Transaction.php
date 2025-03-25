<?php

namespace App\Models;

use App\Enums\PaymentTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'role_type',
        'client_id',
        'user_id',
        'amount',
        'payment_type',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'payment_type' => PaymentTypes::class
    ];

    public function role(): MorphTo
    {
        return $this->morphTo();
    }
}
