<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('cpf_hash', 64)->index()->comment('SHA-256 do CPF para busca rápida sem expor dado');
            $table->text('cpf_encrypted')->comment('CPF Criptografado (AES-256)');
            $table->text('nome_encrypted')->comment('Nome Criptografado (AES-256)');
            $table->date('data_nascimento');
            $table->enum('sexo', ['M', 'F', 'OUTRO']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};