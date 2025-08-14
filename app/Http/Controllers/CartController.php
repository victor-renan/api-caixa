<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        parent::__construct(CartItem::class);
    }

    public function getUserCartItems()
    {
        return request()->user()->cart->items();
    }

    public function items(Request $request)
    {
        return response()->json($this->getUserCartItems()->get());
    }

    public function addItem(CartItemRequest $request)
    {
        $data = $request->validated();
        $cartItems = $this->getUserCartItems();
        $existingItem = $cartItems->where('product_id', $data['product_id']);
        
        if ($existingItem->exists()) {
            try {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + 1
                ]);
                return response()->json([
                    'message' => 'Falha ao adicionar unidade a item do carrinho'
                ]);
            } catch (\Throwable $th) {
                logger()->error($th);
                return response()->json([
                    'message' => 'Falaha ao adicionar item ao carrinho, tente mais tarde',
                ]);
            }
        }

        try {
            $item = $cartItems->create($data);
            return response()->json([
                'message' => 'Item adicionado ao carrinho',
                'data' => $item,
            ]);
        } catch (\Throwable $th) {
            logger()->error($th);
            return response()->json([
                'message' => 'Falha ao adicionar item ao carrinho, tente mais tarde',
            ]);
        }
    }

    public function removeItem(Request $request)
    {
        try {
            CartItem::findOrFail($request->id)->delete();
            return response()->json([
                'message' => 'Item removido do carrinho'
            ]);
        } catch (\Throwable $th) {
            logger()->error($th);
            return response()->json([
                'message' => 'Falha ao remover item do carrinho'
            ]);
        }
    }
}
