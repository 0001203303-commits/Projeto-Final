<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('totens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades')->cascadeOnDelete();
            $table->string('codigo_totem')->unique()->comment('Ex: TOTEM-UPA-01');
            $table->string('descricao')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('ultimo_ping_at')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('totens');
    }
};