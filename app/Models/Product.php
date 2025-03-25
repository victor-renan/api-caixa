<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'quantity',
        'image_url',
    ];

    protected function code(): Attribute
    {
        return Attribute::make(
            set: fn () => Str::upper(Str::random(8))
        );
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'role');
    }
}
