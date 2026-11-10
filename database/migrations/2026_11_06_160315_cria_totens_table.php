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
            $table->string('local', 150)->nullable();
            $table->text('observacoes')->nullable();
            $table->string('token_hash', 64)->unique();
            $table->string('token_prefixo', 12);
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_contato_em')->nullable();
            $table->string('ultimo_ip', 45)->nullable();
            $table->string('versao', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('totens');
    }
};
