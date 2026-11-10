<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE triagens MODIFY status ENUM('aguardando','em_atendimento','finalizada','nao_compareceu') NOT NULL DEFAULT 'aguardando'");

        Schema::table('triagens', function (Blueprint $table) {
            $table->enum('desfecho', ['alta', 'observacao', 'internacao', 'encaminhamento'])
                ->nullable()
                ->after('finalizado_em');
        });
    }

    public function down(): void
    {
        Schema::table('triagens', fn (Blueprint $table) => $table->dropColumn('desfecho'));

        DB::statement("UPDATE triagens SET status = 'finalizada' WHERE status = 'nao_compareceu'");
        DB::statement("ALTER TABLE triagens MODIFY status ENUM('aguardando','em_atendimento','finalizada') NOT NULL DEFAULT 'aguardando'");
    }
};
