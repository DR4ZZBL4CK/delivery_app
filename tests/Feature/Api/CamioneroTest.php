<?php

namespace Tests\Feature\Api;

use App\Models\Camion;
use App\Models\Camionero;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CamioneroTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_list_camioneros(): void
    {
        Camionero::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/camioneros');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'documento', 'nombre', 'apellido', 'licencia', 'telefono']
                ],
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_create_camionero(): void
    {
        $camioneroData = [
            'documento' => '12345678',
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'fecha_nacimiento' => '1985-03-15',
            'licencia' => 'A123456',
            'telefono' => '3001234567',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/camioneros', $camioneroData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'documento', 'nombre', 'apellido', 'licencia', 'telefono'
            ]);

        $this->assertDatabaseHas('camioneros', [
            'documento' => '12345678',
        ]);
    }

    public function test_can_show_camionero(): void
    {
        $camionero = Camionero::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/camioneros/{$camionero->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'documento', 'nombre', 'apellido', 'licencia', 'telefono'
            ]);
    }

    public function test_can_update_camionero(): void
    {
        $camionero = Camionero::factory()->create();

        $updateData = [
            'nombre' => 'Juan Carlos',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/camioneros/{$camionero->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('camioneros', [
            'id' => $camionero->id,
            'nombre' => 'Juan Carlos',
        ]);
    }

    public function test_can_delete_camionero(): void
    {
        $camionero = Camionero::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/camioneros/{$camionero->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Eliminado']);

        $this->assertDatabaseMissing('camioneros', [
            'id' => $camionero->id,
        ]);
    }

    public function test_can_assign_camiones_to_camionero(): void
    {
        $camionero = Camionero::factory()->create();
        $camiones = Camion::factory()->count(2)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/camioneros/{$camionero->id}", [
            'camiones' => $camiones->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200);

        $this->assertEquals(2, $camionero->fresh()->camiones()->count());
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/camioneros');
        $response->assertStatus(401);
    }
}
