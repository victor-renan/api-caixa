<?php

namespace App\Services;

use App\Models\Product;

class ProductService extends Crud
{
  public function __construct()
  {
    parent::__construct(Product::class);
  }
}