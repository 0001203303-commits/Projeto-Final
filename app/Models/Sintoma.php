<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;   

class Sintoma extends Model
{
    protected $table = 'sintomas';

    protected $fillable = ['categoria_id', 'nome', 'pontos_base', 'ativo', 'cor_minima'];

    protected $casts = ['ativo' => 'boolean'];

    

    public function triagens(): BelongsToMany
    {
        return $this->belongsToMany(Triagem::class, 'triagem_sintomas');
    }
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaSintoma::class, 'categoria_id');
    }

    public function perguntas(): HasMany
    {
        return $this->hasMany(Pergunta::class);
    }
}