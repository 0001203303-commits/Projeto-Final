<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Pacientes extends Model
{
    protected $fillable = [
        'nome',
        'cpf',
        'protocolo',
        'telefone',
        'data_nascimento',
        'idade',
        'sexo',
        'tipo_sanguineo',
        'urgencia',
        'sintomas',
        'antecedentes_pessoais',
    ];

    protected $casts = [
        'sintomas' => 'array',
    ];
    


}