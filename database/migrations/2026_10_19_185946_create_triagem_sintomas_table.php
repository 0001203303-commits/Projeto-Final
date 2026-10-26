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
        Schema::create('triagem_sintomas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triagem_id')->constrained('triagens')->cascadeOnDelete();
            $table->foreignId('sintoma_id')->constrained('sintomas')->cascadeOnDelete();
            $table->unique(['triagem_id', 'sintoma_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triagem_sintomas');
    }
};
