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
        Schema::create('detalles_paquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paquetes_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipo_mercancia_id')->constrained('tipo_mercancia')->onDelete('cascade');
            $table->string('dimencion', 45);
            $table->string('peso', 45);
            $table->date('fecha_entrega');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_paquetes');
    }
};
