<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'product_id',
    'quantity',
  ];

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }
}
