<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('ia_eventos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('triagem_id')->nullable()->constrained('triagens')->nullOnDelete();
        $table->boolean('sucesso');
        $table->unsignedSmallInteger('status_http')->nullable();
        $table->unsignedInteger('duracao_ms')->nullable();
        $table->string('modelo', 80)->nullable();
        $table->string('mensagem', 300)->nullable();
        $table->timestamp('created_at')->useCurrent();
    });
}

public function down(): void
{
    Schema::dropIfExists('ia_eventos');
}
};
