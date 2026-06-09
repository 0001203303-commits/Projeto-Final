<?php

// app/Models/TriagemCategoria.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TriagemCategoria extends Model
{
    protected $table = 'triagem_categorias';
    protected $fillable = ['nome', 'slug', 'icon', 'css_class', 'parent_id'];

    // Relacionamento para pegar as subcategorias de uma categoria pai
    public function subcategorias(): HasMany
    {
        return $this->hasMany(TriagemCategoria::class, 'parent_id');
    }

    // Relacionamento para pegar as perguntas se esta for uma subcategoria final
    public function perguntas(): HasMany
    {
        return $this->hasMany(TriagemPergunta::class, 'categoria_id');
    }
}

?>