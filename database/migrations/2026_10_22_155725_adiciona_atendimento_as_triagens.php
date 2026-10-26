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
            $table->foreignId('atendido_por')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('chamado_em')->nullable()->after('atendido_por');
            $table->timestamp('finalizado_em')->nullable()->after('chamado_em');
        });
    }

    public function down(): void
    {
        Schema::table('triagens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('atendido_por');
            $table->dropColumn(['chamado_em', 'finalizado_em']);
        });
    }
};
