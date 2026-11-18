<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Triagem;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ResultadoTriagemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('pacientes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('codigo_anonimo')->unique();
            $table->string('nome');
            $table->string('cpf', 11)->unique();
            $table->date('data_nascimento');
            $table->timestamps();
        });

        Schema::create('triagens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('paciente_id');
            $table->string('cor')->nullable();
            $table->boolean('alerta_imediato')->default(false);
            $table->timestamps();
        });
    }

    public function test_resultado_exibe_a_view_com_o_css_do_totem(): void
    {
        $paciente = Paciente::create([
            'nome' => 'Paciente de teste',
            'cpf' => '12345678901',
            'data_nascimento' => '2000-01-01',
        ]);
        $triagem = Triagem::create([
            'paciente_id' => $paciente->id,
            'cor' => 'verde',
        ]);

        $response = $this->withSession(['triagem_id' => $triagem->id])
            ->get(route('triagem.resultado'));

        $response->assertOk()
            ->assertSee('Triagem concluída')
            ->assertSee('cor-verde')
            ->assertSee('css/totem.css');
    }
}
