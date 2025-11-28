<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'admin@test.com')->first();

if ($user) {
    echo "User found:\n";
    echo "ID: " . $user->id . "\n";
    echo "Name: " . $user->nombre . " " . $user->apellido . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Role: " . $user->role . "\n";
} else {
    echo "User admin@test.com NOT found.\n";
}
