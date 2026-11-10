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
        public function comentarios(): HasMany
    {
        return $this->hasMany(TriagemComentario::class);
    }

    /** Resumo da IA sem símbolos de formatação (**negrito**, # títulos etc.). */
    public function getResumoTextoAttribute(): string
    {
        $texto = $this->resumo_ia['resumo'] ?? '';

        return trim(preg_replace('/\*{1,3}|_{2,}|`|^#+\s*/m', '', $texto));
    }

        /** Minutos entre a chegada e o chamado (null se ainda não foi chamado). */
    public function getEsperaAteChamadoAttribute(): ?int
    {
        return $this->chamado_em
            ? (int) floor(abs($this->created_at->diffInMinutes($this->chamado_em)))
            : null;
    }

    /** Minutos entre o chamado e a finalização (null se não finalizou). */
    public function getDuracaoAtendimentoAttribute(): ?int
    {
        return ($this->chamado_em && $this->finalizado_em)
            ? (int) floor(abs($this->chamado_em->diffInMinutes($this->finalizado_em)))
            : null;
    }
}