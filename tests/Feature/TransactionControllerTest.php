<?php

namespace Tests\Feature;

use App\Enums\PaymentTypes;
use App\Models\Transaction;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
  public function setUp(): void
  {
    parent::setUp();

    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Transaction::factory(10)->create();
  }

  public function test_listage(): void
  {
    $response = $this->getJson('/api/transactions');

    $this->assertCount(10, $response->json()['data']);

    $response->assertJsonStructure([
      'data' => [
        '*' => [
          'user_id',
          'amount',
          'payment_type',
        ]
      ]
    ]);

    $response->assertStatus(200);
  }

  public function test_listage_search(): void
  {
    Transaction::query()->delete();

    Transaction::factory()->create([
      'amount' => '10.00',
    ]);

    Transaction::factory()->create([
        'amount' => '20.00',
    ]);

    Transaction::factory()->create([
        'amount' => '30.00',
    ]);

    $response = $this->getJson('/api/transactions?amount_min=30');
    $response->assertStatus(200);
    $this->assertCount(1, $response->json()['data']);

    $response = $this->getJson('/api/transactions?amount_max=30');
    $response->assertStatus(200);
    $this->assertCount(3, $response->json()['data']);
  }

  public function test_details(): void
  {
    $transaction = Transaction::first();

    $response = $this->getJson("/api/transactions/$transaction->id");

    $response->assertStatus(200);
    
    $response->assertJsonStructure([
      'user_id',
      'amount',
      'payment_type',
    ]);
  }

  public function test_details_invalid(): void
  {

    Transaction::query()->delete();

    $response = $this->getJson('/api/transactions/1');

    $response->assertStatus(404);
  }

  public function test_create(): void
  {

    Transaction::query()->delete();

    $response = $this->putJson('/api/transactions', [
      'user_id' => User::factory()->create()->id,
      'amount' => '10.00',
      'payment_type' => PaymentTypes::Pix
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            'user_id',
            'amount',
            'payment_type',
        ]
    ]);

    $this->assertDatabaseCount(Transaction::class, 1);
  }

  public function test_delete(): void
  {
    $transaction = Transaction::factory()->create();

    $response = $this->deleteJson("/api/transactions/$transaction->id");
    $response->assertStatus(200);

    $this->assertDatabaseMissing($transaction);
  }
}
