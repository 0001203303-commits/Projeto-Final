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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf');
            $table->string('idade')->nullable();
            $table->string('horario')->nullable();
            $table->string('status')->nullable();
            $table->string('tipo_sanguineo')->nullable();
            $table->string('data_nascimento')->nullable();
            $table->string('sexo')->nullable();
            $table->string('telefone')->nullable();
            $table->string('endereco')->nullable();
            $table->string('email')->nullable();
            $table->text('antecedentes_pessoais')->nullable();
            $table->text('sintomas')->nullable();
            $table->string('urgencia')->nullable();
            $table->string('protocolo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
