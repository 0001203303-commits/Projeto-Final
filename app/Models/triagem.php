<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Triagem extends Model
{
    use HasFactory;

    protected $table = 'triagem';
    protected $fillable = [
        'nome',
        'cpf',
        'idade',
        'horario',
        'tipo_sanguineo',
        'data_nascimento',
        'sexo',
        'telefone',
        'endereco',
        'email',
        'antecedentes_pessoais',
        'sintomas',
        'urgencia',
        'status'
    ];
}

