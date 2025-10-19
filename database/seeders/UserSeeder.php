<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        $user = User::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Crear algunos contactos de ejemplo para el usuario
        $contactos = [
            [
                'nombre' => 'María',
                'apellido' => 'García',
                'telefono' => '+34123456789',
                'email' => 'maria.garcia@email.com',
                'direccion' => 'Calle Principal 123, Madrid, España'
            ],
            [
                'nombre' => 'Carlos',
                'apellido' => 'López',
                'telefono' => '+34987654321',
                'email' => 'carlos.lopez@email.com',
                'direccion' => 'Avenida Central 456, Barcelona, España'
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Martínez',
                'telefono' => '+34555666777',
                'email' => 'ana.martinez@email.com',
                'direccion' => 'Plaza Mayor 789, Valencia, España'
            ],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Sánchez',
                'telefono' => '+34444333222',
                'email' => 'roberto.sanchez@email.com',
                'direccion' => null
            ],
            [
                'nombre' => 'Laura',
                'apellido' => 'Fernández',
                'telefono' => '+34777888999',
                'email' => 'laura.fernandez@email.com',
                'direccion' => 'Calle de la Paz 321, Sevilla, España'
            ]
        ];

        foreach ($contactos as $contacto) {
            $user->contacts()->create($contacto);
        }

        $this->command->info('✅ Usuario y contactos de prueba creados exitosamente!');
        $this->command->info('📧 Email: admin@test.com');
        $this->command->info('🔑 Contraseña: password123');
    }
}
