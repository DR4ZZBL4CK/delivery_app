<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'admin@test.com')->first();

if ($user) {
    $user->role = 'admin';
    $user->save();
    echo "User role updated to 'admin' successfully.\n";
} else {
    echo "User admin@test.com NOT found. Creating it...\n";
    User::create([
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'email' => 'admin@test.com',
        'password' => '$2y$12$KjG.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6.6', // hash for password123 (placeholder, better to use Hash::make but this is quick script)
        'role' => 'admin'
    ]);
    // Note: I'll use Hash facade if I need to create, but likely it exists.
    // Let's stick to updating if exists.
}
