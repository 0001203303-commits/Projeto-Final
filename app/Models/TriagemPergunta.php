<?php

// app/Models/TriagemPergunta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriagemPergunta extends Model
{
    protected $table = 'triagem_perguntas';
    protected $fillable = ['categoria_id', 'texto_pergunta', 'score_manchester'];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(TriagemCategoria::class, 'categoria_id');
    }
}

?>