<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
  use HasFactory;

  protected $fillable = [
      'user_id',
      'subject_id',
      'subject_type',
      'quantity',
  ];

  public function subject(): MorphTo
  {
    return $this->morphTo();
  }
}
