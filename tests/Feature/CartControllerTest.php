<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

    }
    
    public function test_cart_create(): void
    {
        Product::factory()->create();

        $res = $this->postJson('/api/cart', [
            'product_id' => 1,
            'quantity' => 1
        ]);

        $res->assertOk();
    }

    public function test_list_cart_items(): void
    {
        CartItem::factory()->create([
            'cart_id' => \Auth::user()->cart->id,
        ]);

        $res = $this->getJson('/api/cart');
        $res->assertOk();

        $this->assertCount(1, $res->json());
    }

    public function test_remove_cart_items(): void
    {
        $item = CartItem::factory()->create([
            'cart_id' => \Auth::user()->cart->id,
        ]);


        $res = $this->deleteJson('/api/cart/'.$item->id);
        $res->assertOk();

        $this->assertModelMissing($item);
    }
}