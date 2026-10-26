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
        Schema::create('triagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->unsignedSmallInteger('bpm')->nullable();
            $table->unsignedTinyInteger('spo2')->nullable();
            $table->unsignedSmallInteger('pa_sistolica')->nullable();
            $table->unsignedSmallInteger('pa_diastolica')->nullable();
            $table->decimal('temperatura', 3, 1)->nullable();
            $table->unsignedSmallInteger('score_sintomas')->default(0);
            $table->unsignedSmallInteger('score_vitais')->default(0);
            $table->unsignedSmallInteger('score_total')->default(0);
            $table->enum('cor', ['laranja', 'amarelo', 'verde', 'azul'])->nullable();
            $table->enum('status', ['aguardando', 'em_atendimento', 'finalizada'])->default('aguardando');
            $table->text('resumo_ia')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triagems');
    }
};
