<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('triagem_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triagem_id')->constrained('triagens')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('autor_nome', 150);
            $table->text('texto');
            $table->timestamps();

            $table->index(['triagem_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triagem_comentarios');
    }
};
