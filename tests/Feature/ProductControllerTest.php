<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
  public function setUp(): void
  {
    parent::setUp();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Storage::fake();
    
    Product::factory(10)->create();
  }


  public function test_create_with_photo(): void 
  {
    $res = $this->putJson('/api/products', [
      'name' => 'Teste',
      'description' => 'Teste',
      'price' => '1.0',
      'code' => 'code',
      'quantity' => 1,
      'image' => UploadedFile::fake()->image('teste.png')  
    ]);

    $this->assertNotNull($res->json()['data']['image_url']);
  }

  public function test_update_with_photo(): void 
  {
    $p = Product::factory()->create([
      'image_url' => UploadedFile::fake()->image('teste2.png')->store('products')  
    ]);

    $res = $this->patchJson("/api/products/{$p->id}", [
      'image' => UploadedFile::fake()->image('teste3.png')  
    ]);

    $this->assertNotEquals($p->image_url, $res->json()['data']['image_url']);
  }
  public function test_delete_with_photo(): void 
  {
    $p = Product::factory()->create([
      'image_url' => UploadedFile::fake()->image('teste2.png')->store('products')  
    ]);

    $this->deleteJson("/api/products/{$p->id}");

    Storage::assertMissing($p->image_url);
  } 
}
