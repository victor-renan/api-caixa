<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Client::factory(10)->create();
    }
    public function test_listage(): void
    {
        $response = $this->getJson('/api/clients');

        $this->assertCount(10, $response->json()['data']);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'phone',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

        $response->assertStatus(200);
    }

    public function test_listage_search(): void
    {
        Client::query()->delete();

        Client::factory()->create([
            'name' => 'Teste',
            'phone' => '12345678'
        ]);

        Client::factory()->create([
            'name' => 'Etset',
            'phone' => '87654321'
        ]);

        $response = $this->getJson('/api/clients?name=Test');
        $this->assertCount(1, $response->json()['data']);

        $response = $this->getJson('/api/clients?phone=123456');
        $this->assertCount(1, $response->json()['data']);

        $response = $this->getJson('/api/clients?per_page=6');
        $this->assertEquals(6, $response->json()['per_page']);

        $response = $this->getJson('/api/clients?per_page=4');
        $this->assertEquals(15, $response->json()['per_page']);

        $response->assertStatus(200);
    }

    public function test_details(): void
    {

        $client = Client::first();

        $response = $this->getJson("/api/clients/$client->id");

        $response->assertJsonStructure([
            'name',
            'phone',
            'created_at',
            'updated_at',
        ]);

        $response->assertStatus(200);
    }
}
