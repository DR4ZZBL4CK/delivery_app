<?php

namespace Tests\Feature\Api;

use App\Models\Camionero;
use App\Models\EstadoPaquete;
use App\Models\Paquete;
use App\Models\TipoMercancia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaqueteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    private User $admin;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->adminToken = $this->admin->createToken('admin-token')->plainTextToken;
    }

    public function test_can_list_paquetes(): void
    {
        $camionero = Camionero::factory()->create();
        $estado = EstadoPaquete::factory()->create();
        
        Paquete::factory()->create([
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/paquetes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'direccion', 'camionero', 'estado', 'detalles']
                ],
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_admin_can_create_paquete(): void
    {
        $camionero = Camionero::factory()->create();
        $estado = EstadoPaquete::factory()->create();
        $tipo = TipoMercancia::factory()->create();

        $paqueteData = [
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
            'direccion' => 'Calle 123 #45-67',
            'detalles' => [
                [
                    'tipo_mercancia_id' => $tipo->id,
                    'dimencion' => '50x30x40 cm',
                    'peso' => '15 kg',
                    'fecha_entrega' => now()->addDays(2)->format('Y-m-d'),
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->postJson('/api/paquetes', $paqueteData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'direccion', 'camionero', 'estado', 'detalles'
            ]);

        $this->assertDatabaseHas('paquetes', [
            'direccion' => 'Calle 123 #45-67',
        ]);
    }

    public function test_can_show_paquete(): void
    {
        $camionero = Camionero::factory()->create();
        $estado = EstadoPaquete::factory()->create();
        
        $paquete = Paquete::factory()->create([
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/paquetes/{$paquete->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'direccion', 'camionero', 'estado', 'detalles'
            ]);
    }

    public function test_admin_can_update_paquete(): void
    {
        $camionero = Camionero::factory()->create();
        $estado = EstadoPaquete::factory()->create();
        
        $paquete = Paquete::factory()->create([
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
        ]);

        $updateData = [
            'direccion' => 'Nueva dirección 456',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->putJson("/api/paquetes/{$paquete->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('paquetes', [
            'id' => $paquete->id,
            'direccion' => 'Nueva dirección 456',
        ]);
    }

    public function test_admin_can_delete_paquete(): void
    {
        $camionero = Camionero::factory()->create();
        $estado = EstadoPaquete::factory()->create();
        
        $paquete = Paquete::factory()->create([
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->adminToken,
        ])->deleteJson("/api/paquetes/{$paquete->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Eliminado']);

        $this->assertDatabaseMissing('paquetes', [
            'id' => $paquete->id,
        ]);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/paquetes');
        $response->assertStatus(401);
    }

    public function test_user_cannot_create_paquete(): void
    {
        $camionero = \App\Models\Camionero::factory()->create();
        $estado = \App\Models\EstadoPaquete::factory()->create();
        $tipo = \App\Models\TipoMercancia::factory()->create();

        $paqueteData = [
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
            'direccion' => 'Calle 123 #45-67',
            'detalles' => [
                [
                    'tipo_mercancia_id' => $tipo->id,
                    'dimencion' => '50x30x40 cm',
                    'peso' => '15 kg',
                    'fecha_entrega' => now()->addDays(2)->format('Y-m-d'),
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/paquetes', $paqueteData);

        $response->assertStatus(403);
    }

    public function test_user_cannot_update_paquete(): void
    {
        $camionero = \App\Models\Camionero::factory()->create();
        $estado = \App\Models\EstadoPaquete::factory()->create();
        $paquete = Paquete::factory()->create([
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/paquetes/{$paquete->id}", ['direccion' => 'Nueva dirección']);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_paquete(): void
    {
        $camionero = \App\Models\Camionero::factory()->create();
        $estado = \App\Models\EstadoPaquete::factory()->create();
        $paquete = Paquete::factory()->create([
            'camioneros_id' => $camionero->id,
            'estados_paquetes_id' => $estado->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/paquetes/{$paquete->id}");

        $response->assertStatus(403);
    }
}
