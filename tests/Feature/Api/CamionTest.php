<?php

namespace Tests\Feature\Api;

use App\Models\Camion;
use App\Models\Camionero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CamionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;
    private string $token;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->adminToken = $this->admin->createToken('admin-token')->plainTextToken;
    }

    public function test_can_list_camiones(): void
    {
        Camion::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/camiones');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'placa', 'modelo']
                ],
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_show_camion(): void
    {
        $camion = Camion::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/camiones/{$camion->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'placa', 'modelo'
            ]);
    }

    public function test_admin_can_create_camion(): void
    {
        $camionData = [
            'placa' => 'ABC123',
            'modelo' => '2020',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/camiones', $camionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'placa', 'modelo'
            ]);

        $this->assertDatabaseHas('camiones', [
            'placa' => 'ABC123',
        ]);
    }

    public function test_user_cannot_create_camion(): void
    {
        $camionData = [
            'placa' => 'XYZ789',
            'modelo' => '2021',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/camiones', $camionData);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_camion(): void
    {
        $camion = Camion::factory()->create();

        $updateData = [
            'modelo' => '2022',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/camiones/{$camion->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('camiones', [
            'id' => $camion->id,
            'modelo' => '2022',
        ]);
    }

    public function test_user_cannot_update_camion(): void
    {
        $camion = Camion::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/camiones/{$camion->id}", ['modelo' => '2022']);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_camion(): void
    {
        $camion = Camion::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/camiones/{$camion->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Eliminado']);

        $this->assertDatabaseMissing('camiones', [
            'id' => $camion->id,
        ]);
    }

    public function test_user_cannot_delete_camion(): void
    {
        $camion = Camion::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/camiones/{$camion->id}");

        $response->assertStatus(403);
    }

    public function test_can_assign_camioneros_to_camion(): void
    {
        $camion = Camion::factory()->create();
        $camioneros = Camionero::factory()->count(2)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/camiones/{$camion->id}", [
            'camioneros' => $camioneros->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200);

        $this->assertEquals(2, $camion->fresh()->camioneros()->count());
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/camiones');
        $response->assertStatus(401);
    }

    public function test_validation_prevents_duplicate_placa(): void
    {
        Camion::factory()->create(['placa' => 'ABC123']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/camiones', [
            'placa' => 'ABC123',
            'modelo' => '2020',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['placa']);
    }
}

