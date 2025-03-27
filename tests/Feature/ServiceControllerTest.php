<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Service::factory(10)->create();
    }

    public function test_listage(): void
    {
        $response = $this->getJson('/api/services');

        $response->assertStatus(200);

        $this->assertCount(10, $response->json()['data']);
        
        $response->assertJsonStructure([
          'data' => [
                '*' => [
                    'name',
                    'description',
                    'price',
                ]
            ]
        ]);

    }

    public function test_listage_search(): void
    {
        Service::query()->delete();

        Service::factory()->create([
            'name' => 'Service A',
            'description' => 'Description A',
        ]);

        Service::factory()->create([
            'name' => 'Service B',
            'description' => 'Description B',
        ]);

        Service::factory()->create([
            'name' => 'Service C',
            'description' => 'Description C',
        ]);

        $response = $this->getJson('/api/services?name=Service A');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['data']);

        $response = $this->getJson('/api/services?description=Description B');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json()['data']);
    }

    public function test_details(): void
    {
        $service = Service::first();

        $response = $this->getJson("/api/services/$service->id");

        $response->assertStatus(200);
        
        $response->assertJsonStructure([
            'name',
            'description',
            'price',
        ]);
    }

    public function test_details_invalid(): void
    {
        Service::query()->delete();

        $response = $this->getJson('/api/services/1');

        $response->assertStatus(404);
    }

    public function test_create(): void
    {
        Service::query()->delete();

        $response = $this->putJson('/api/services', [
            'name' => 'New Service',
            'description' => 'New Service Description',
            'price' => 'R$ 100,00',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'description',
                'price',
            ],
        ]);

        $this->assertDatabaseCount(Service::class, 1);
    }

    public function test_update(): void
    {
        $service = Service::factory()->create([
            'name' => 'Old Service',
        ]);

        $response = $this->patchJson("/api/services/$service->id", [
            'name' => 'Updated Service',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'description',
                'price',
            ],
        ]);

        $this->assertEquals($response->json()['data']['name'], 'Updated Service');
    }

    public function test_delete(): void
    {
        $service = Service::factory()->create([
            'name' => 'Service to Delete',
        ]);

        $response = $this->deleteJson("/api/services/$service->id");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}