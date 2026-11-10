<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Totem extends Model
{
    // O Laravel deduziria "totems"; a tabela se chama "totens"
    protected $table = 'totens';

    protected $fillable = ['nome', 'local', 'observacoes'];

    protected $hidden = ['token_hash'];

    protected $casts = [
        'ativo'             => 'boolean',
        'ultimo_contato_em' => 'datetime',
    ];

    /** Gera um token novo, guarda só o hash e devolve o texto original (mostrado uma única vez). */
    public function gerarToken(): string
    {
        $token = 'tt_' . Str::random(40);

        $this->token_hash = hash('sha256', $token);
        $this->token_prefixo = substr($token, 0, 9);

        return $token;
    }

    /** online | offline | nunca | inativo */
    public function getStatusConexaoAttribute(): string
    {
        if (! $this->ativo) {
            return 'inativo';
        }

        if (! $this->ultimo_contato_em) {
            return 'nunca';
        }

        $limite = now()->subSeconds((int) config('triagem.totem_online_segundos', 120));

        return $this->ultimo_contato_em->gte($limite) ? 'online' : 'offline';
    }
}