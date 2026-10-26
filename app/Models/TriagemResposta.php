<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriagemResposta extends Model
{
    protected $table = 'triagem_respostas';

    public $timestamps = false;

    protected $fillable = ['triagem_id', 'pergunta_id', 'valor', 'pontos'];

    public function pergunta(): BelongsTo
    {
        return $this->belongsTo(Pergunta::class);
    }

        public function getRotuloAttribute(): string
    {
        if ($this->pergunta->tipo === 'intensidade') {
            return [1 => 'Leve', 2 => 'Moderada', 3 => 'Forte'][$this->valor] ?? '';
        }

        return (int) $this->valor === 1 ? 'Sim' : 'Não';
    }
}