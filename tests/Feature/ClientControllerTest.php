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
        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['data']);

        $response = $this->getJson('/api/clients?phone=123456');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['data']);

        $response = $this->getJson('/api/clients?per_page=6');
        $response->assertStatus(200);
        $this->assertEquals(6, $response->json()['per_page']);

        $response = $this->getJson('/api/clients?per_page=4');
        $response->assertStatus(200);
        $this->assertEquals(15, $response->json()['per_page']);

    }

    public function test_details(): void
    {

        $client = Client::first();

        $response = $this->getJson("/api/clients/$client->id");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'name',
            'phone',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_details_invalid(): void
    {

        Client::query()->delete();

        $response = $this->getJson('/api/clients/1');

        $response->assertStatus(404);
    }

    public function test_create(): void
    {

        $response = $this->putJson('/api/clients/', [
            'name' => 'Test',
            'phone' => '12345678',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'phone',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals($response->json()['data']['phone'], '12345678');
    }

    public function test_update(): void
    {
        $client = Client::factory()->create([
            'name' => 'Foo',
        ]);

        $response = $this->patchJson("/api/clients/$client->id", [
            'name' => 'Bar',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'phone',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertEquals($response->json()['data']['name'], 'Bar');
    }

    public function test_delete(): void
    {
        $client = Client::factory()->create([
            'name' => 'Foo',
        ]);

        $response = $this->deleteJson("/api/clients/$client->id");
        $response->assertStatus(200);

        $this->assertDatabaseMissing($client);
    }
}
