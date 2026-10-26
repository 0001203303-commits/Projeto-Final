<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Administrador',     'email' => 'admin@smarttriage.local', 'perfil' => 'admin'],
            ['name' => 'Médico Plantonista', 'email' => 'medico@smarttriage.local', 'perfil' => 'medico'],
            ['name' => 'Técnico de TI',     'email' => 'ti@smarttriage.local',    'perfil' => 'ti'],
        ];

        foreach ($usuarios as $dados) {
            if (User::where('email', $dados['email'])->exists()) {
                continue;
            }

            $senha = Str::password(12, symbols: false);

            User::create($dados + ['password' => Hash::make($senha), 'ativo' => true]);

            $this->command->warn("{$dados['perfil']}: {$dados['email']}  |  senha: {$senha}");
        }
    }
}