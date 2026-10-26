<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Paciente extends Model
{
    protected $fillable = [
        'nome', 'cpf', 'data_nascimento', 'sexo', 'telefone', 'gestante',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'gestante' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Código anônimo: é o que será enviado à IA, nunca nome ou CPF
        static::creating(function (Paciente $paciente) {
            $paciente->codigo_anonimo ??= (string) Str::uuid();
        });
    }

    public function setCpfAttribute(string $value): void
    {
        $this->attributes['cpf'] = preg_replace('/\D/', '', $value);
    }

    public function setTelefoneAttribute(?string $value): void
    {
        $this->attributes['telefone'] = $value ? preg_replace('/\D/', '', $value) : null;
    }

    public function getIdadeAttribute(): int
    {
        return $this->data_nascimento->age;
    }

    public function triagens(): HasMany
    {
        return $this->hasMany(Triagem::class);
    }

        public function getCpfFormatadoAttribute(): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    
}
