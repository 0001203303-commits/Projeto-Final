<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Pergunta extends Model
{
    protected $table = 'perguntas';

    protected $fillable = ['sintoma_id', 'texto', 'tipo', 'pontos_sim', 'ordem', 'ativo','cor_minima'];

    protected $casts = ['ativo' => 'boolean'];

    public function sintoma(): BelongsTo
    {
        return $this->belongsTo(Sintoma::class);
    }


    public function respostas(): HasMany
    {
        return $this->hasMany(TriagemResposta::class);
    }
}