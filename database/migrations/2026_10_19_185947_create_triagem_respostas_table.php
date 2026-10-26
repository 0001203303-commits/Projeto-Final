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
        Schema::create('triagem_respostas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triagem_id')->constrained('triagens')->cascadeOnDelete();
            $table->foreignId('pergunta_id')->constrained('perguntas')->cascadeOnDelete();
            $table->unsignedTinyInteger('valor')->default(0);
            $table->unsignedTinyInteger('pontos')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triagem_respostas');
    }
};
