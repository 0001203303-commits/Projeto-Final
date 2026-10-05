<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('triagens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidade_id')->constrained('unidades');
            $table->foreignId('totem_id')->constrained('totens');
            $table->foreignId('paciente_id')->constrained('pacientes');
            
            $table->string('senha_atendimento', 20)->comment('Ex: VERM-001, AMA-042');
            $table->string('queixa_principal');
            $table->json('questionario_respostas')->comment('Respostas estruturadas do atendimento');
            
            $table->string('classificacao_risco', 20)->comment('Cor do Protocolo de Manchester');
            $table->string('status', 30)->default('AGUARDANDO_IA');
            
            $table->timestamp('data_atendimento')->useCurrent();
            $table->timestamps();

            $table->index(['unidade_id', 'status']);
            $table->index(['unidade_id', 'classificacao_risco']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triagens');
    }
};