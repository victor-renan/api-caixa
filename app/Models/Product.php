<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = [
        'image_asset'
    ];

    protected function imageAsset(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => asset($attributes['image_url']) 
        );
    }

    public function cartItems(): MorphMany
    {
        return $this->morphMany(CartItem::class, 'subject');
    }
}
