<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'code',
        'quantity',
        'image_url',
    ];

    protected $casts = [
      'quantity' => 'integer'
    ];

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'role');
    }
}
