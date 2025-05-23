<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function __construct()
    {
        parent::__construct(CartItem::class);
    }
}
