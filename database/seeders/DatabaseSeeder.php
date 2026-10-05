<?php

namespace Database\Seeders;

use App\Models\Totem;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $unidade = Unidade::create([
            'nome' => 'UPA 24h - Central',
            'codigo_cnes' => '1234567',
            'ativo' => true,
        ]);

        $totem = Totem::create([
            'unidade_id' => $unidade->id,
            'codigo_totem' => 'TOTEM-UPA-01',
            'descricao' => 'Totem Entrada Principal',
            'ip_address' => '192.168.1.50',
            'ativo' => true,
        ]);

        $token = $totem->createToken('totem-auth-token')->plainTextToken;

        $this->command->info("Unidade e Totem criados com sucesso!");
        $this->command->warn("Token do Totem (Use no Header Authorization: Bearer {$token}):");
        $this->command->line($token);
    }
}