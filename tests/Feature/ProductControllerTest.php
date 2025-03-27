<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
  public function setUp(): void
  {
    parent::setUp();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Product::factory(10)->create();
  }

  public function test_listage(): void
  {
    $response = $this->getJson('/api/products');

    $this->assertCount(10, $response->json()['data']);

    $response->assertJsonStructure([
      'data' => [
        '*' => [
          'name',
          'description',
          'quantity',
          'price',
        ]
      ]
    ]);

    $response->assertStatus(200);
  }

  public function test_listage_search(): void
  {
    Product::query()->delete();

    Product::factory()->create([
      'name' => 'Foo',
      'description' => 'Lorem Ipsum',
      'code' => 'ASDF',
    ]);

    Product::factory()->create([
      'name' => 'Bar',
      'description' => 'Dolor Sit',
      'code' => 'FDSA',
    ]);

    Product::factory()->create([
      'name' => 'Baz',
      'description' => 'Amet Consequtour',
      'code' => '12345678'
    ]);

    $response = $this->getJson('/api/products?name=Fo');
    $response->assertStatus(200);
    $this->assertCount(1, $response->json()['data']);

    $response = $this->getJson('/api/products?description=Lorem');
    $response->assertStatus(200);
    $this->assertCount(1, $response->json()['data']);

    $response = $this->getJson('/api/products?code=123456');
    $response->assertStatus(200);
    $this->assertCount(1, $response->json()['data']);

    $response = $this->getJson('/api/products?per_page=6');
    $response->assertStatus(200);
    $this->assertEquals(6, $response->json()['per_page']);

    $response = $this->getJson('/api/products?per_page=4');
    $response->assertStatus(200);
    $this->assertEquals(15, $response->json()['per_page']);

  }

  public function test_details(): void
  {
    $product = Product::first();

    $response = $this->getJson("/api/products/$product->id");

    $response->assertStatus(200);
    
    $response->assertJsonStructure([
      'name',
      'description',
      'code',
      'quantity',
      'price',
    ]);
  }

  public function test_details_invalid(): void
  {

    Product::query()->delete();

    $response = $this->getJson('/api/products/1');

    $response->assertStatus(404);
  }

  public function test_create(): void
  {

    Product::query()->delete();

    $response = $this->putJson('/api/products/', [
      'name' => 'Foo',
      'description' => 'Lorem Ipsum',
      'price' => 'R$ 50,00',
      'code' => '12345678',
      'quantity' => 1
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
      'data' => [
        'name',
        'description',
        'code',
        'quantity',
        'price',
      ],
    ]);

    $this->assertDatabaseCount(Product::class, 1);
  }

  public function test_update(): void
  {
    $product = Product::factory()->create([
      'name' => 'Foo',
    ]);

    $response = $this->patchJson("/api/products/$product->id", [
      'name' => 'Bar',
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
      'data' => [
        'name',
        'description',
        'code',
        'quantity',
        'price',
      ],
    ]);

    $this->assertEquals($response->json()['data']['name'], 'Bar');
  }

  public function test_delete(): void
  {
    $product = Product::factory()->create([
      'name' => 'Foo',
    ]);

    $response = $this->deleteJson("/api/products/$product->id");
    $response->assertStatus(200);

    $this->assertDatabaseMissing($product);
  }
}
