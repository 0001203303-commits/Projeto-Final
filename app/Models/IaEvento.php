<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IaEvento extends Model
{
    protected $table = 'ia_eventos';

    public $timestamps = false;

    protected $fillable = ['triagem_id', 'sucesso', 'status_http', 'duracao_ms', 'modelo', 'mensagem'];

    protected $casts = [
        'sucesso'    => 'boolean',
        'created_at' => 'datetime',
    ];
}