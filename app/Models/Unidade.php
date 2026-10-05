<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidade extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'codigo_cnes', 'ativo'];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function totens(): HasMany
    {
        return $this->hasMany(Totem::class);
    }

    public function triagens(): HasMany
    {
        return $this->hasMany(Triagem::class);
    }
}
?>