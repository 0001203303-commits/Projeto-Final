<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

  
class Triagem extends Model
{
    protected $table = 'triagens';

    protected $fillable = [
        'paciente_id', 'bpm', 'spo2', 'pa_sistolica', 'pa_diastolica',
        'temperatura', 'score_sintomas', 'score_vitais', 'score_total',
        'cor', 'status', 'resumo_ia','atendido_por','chamado_em','finalizado_em',
        'motivos' => 'array',
        'alerta_imediato' => 'boolean',
        'atendido_po' => 'string',
        'chamado_em'    => 'datetime',
        'finalizado_em' => 'datetime',
        

    ];

        protected $casts = [
        'motivos'         => 'array',
        'alerta_imediato' => 'boolean',
        'resumo_ia'       => 'array',
        'chamado_em'    => 'datetime',
        'finalizado_em' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
      // dentro da classe:
    public function sintomas(): BelongsToMany
    {
        return $this->belongsToMany(Sintoma::class, 'triagem_sintomas');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(TriagemResposta::class);
    }

     public function atendente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    public function getEsperaMinutosAttribute(): int
    {
        return (int) floor(abs($this->created_at->diffInMinutes(now())));
    }
}