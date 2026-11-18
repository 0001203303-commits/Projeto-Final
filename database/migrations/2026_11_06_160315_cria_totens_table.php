<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('totens', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->string('localizacao', 150);
            $table->string('descricao', 500)->nullable();
            $table->enum('status', ['online', 'offline', 'manutencao'])->default('offline');
            $table->timestamp('ultimo_sinal')->nullable();
            $table->string('ip_ultimo', 45)->nullable();
            $table->string('versao', 30)->nullable();
            $table->string('token_hash', 64)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('totens');
    }
};
