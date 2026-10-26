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
            $table->uuid('codigo_anonimo')->unique();
            $table->string('nome');
            $table->string('cpf', 11)->unique();
            $table->date('data_nascimento');
            $table->enum('sexo', ['M', 'F', 'O'])->nullable();
            $table->string('telefone', 15)->nullable();
            $table->boolean('gestante')->default(false);
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
