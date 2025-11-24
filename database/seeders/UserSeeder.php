<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Crear/actualizar usuario admin de prueba de forma idempotente
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Crear usuario normal de prueba
        $user = User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        $this->command->info('Usuarios de prueba creados:');
        $this->command->info('Admin - Email: admin@test.com | Contraseña: password123');
        $this->command->info('Usuario - Email: user@test.com | Contraseña: password123');
    }
}
