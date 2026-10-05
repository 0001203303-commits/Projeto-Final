<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sinais_vitais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triagem_id')->unique()->constrained('triagens')->cascadeOnDelete();
            
            $table->integer('spo2')->comment('Saturação de Oxigênio %');
            $table->integer('frequencia_cardiaca')->comment('BPM');
            $table->decimal('temperatura', 4, 1)->comment('Graus Celsius');
            $table->integer('pressao_sistolica')->nullable()->comment('mmHg');
            $table->integer('pressao_diastolica')->nullable()->comment('mmHg');
            $table->decimal('peso', 5, 2)->nullable()->comment('Kg');
            $table->decimal('altura', 3, 2)->nullable()->comment('Metros');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sinais_vitais');
    }
};