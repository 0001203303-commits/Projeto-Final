<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf_hash',
        'cpf_encrypted',
        'nome_encrypted',
        'data_nascimento',
        'sexo',
    ];

    protected function casts(): array
    {
        return [
            'cpf_encrypted' => 'encrypted',
            'nome_encrypted' => 'encrypted',
            'data_nascimento' => 'date',
        ];
    }

    public function getIdadeAttribute(): int
    {
        return Carbon::parse($this->data_nascimento)->age;
    }

    public function triagens(): HasMany
    {
        return $this->hasMany(Triagem::class);
    }
}
?>