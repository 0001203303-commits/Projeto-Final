<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaSintoma extends Model
{
    protected $table = 'categorias_sintomas';

    protected $fillable = ['nome', 'icone', 'ordem', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function sintomas(): HasMany
    {
        return $this->hasMany(Sintoma::class, 'categoria_id');
    }
}