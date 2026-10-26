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
        Schema::table('triagens', function (Blueprint $table) {
            $table->boolean('alerta_imediato')->default(false)->after('cor');
            $table->json('motivos')->nullable()->after('resumo_ia');
        });

        Schema::table('perguntas', function (Blueprint $table) {
            $table->enum('cor_minima', ['amarelo', 'laranja'])->nullable()->after('pontos_sim');
        });

        Schema::table('sintomas', function (Blueprint $table) {
            $table->enum('cor_minima', ['amarelo', 'laranja'])->nullable()->after('pontos_base');
        });
    }

    public function down(): void
    {
        Schema::table('triagens', fn (Blueprint $t) => $t->dropColumn(['alerta_imediato', 'motivos']));
        Schema::table('perguntas', fn (Blueprint $t) => $t->dropColumn('cor_minima'));
        Schema::table('sintomas', fn (Blueprint $t) => $t->dropColumn('cor_minima'));
    }
};
