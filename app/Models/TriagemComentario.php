<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriagemComentario extends Model
{
    protected $table = 'triagem_comentarios';

    protected $fillable = ['triagem_id', 'user_id', 'autor_nome', 'texto'];

    public function triagem(): BelongsTo
    {
        return $this->belongsTo(Triagem::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}