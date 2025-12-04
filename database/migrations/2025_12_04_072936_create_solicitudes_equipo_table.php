<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitudes_equipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('participante_id')->constrained()->onDelete('cascade');
            $table->text('mensaje')->nullable();
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->foreignId('respondida_por_participante_id')
                ->nullable()
                ->constrained('participantes')->onDelete('set null');
            $table->timestamp('respondida_en')->nullable();
            $table->timestamps();
            $table->unique(['equipo_id', 'participante_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_equipo');
    }
};
