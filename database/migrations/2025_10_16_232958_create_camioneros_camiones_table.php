<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('camioneros_camiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camioneros_id')->constrained()->onDelete('cascade');
            $table->foreignId('camiones_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camioneros_camiones');
    }
};
