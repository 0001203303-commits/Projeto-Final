<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriarTriagemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autenticação já validada via Sanctum no middleware da rota
    }

    public function rules(): array
    {
        return [
            'codigo_totem' => ['required', 'string', 'exists:totens,codigo_totem'],

            // Dados do Paciente
            'paciente.cpf' => ['required', 'string', 'regex:/^\d{11}$/'],
            'paciente.nome' => ['required', 'string', 'max:255'],
            'paciente.data_nascimento' => ['required', 'date', 'before:today'],
            'paciente.sexo' => ['required', 'in:M,F,OUTRO'],

            // Anamnese
            'queixa_principal' => ['required', 'string', 'max:255'],
            'questionario' => ['required', 'array'],
            'questionario.intensidade_dor' => ['nullable', 'integer', 'min:0', 'max:10'],
            'questionario.botao_panico' => ['nullable', 'boolean'],

            // Sinais Vitais (Biometria IoMT)
            'sinais_vitais' => ['required', 'array'],
            'sinais_vitais.spo2' => ['required', 'integer', 'between:50,100'],
            'sinais_vitais.frequencia_cardiaca' => ['required', 'integer', 'between:30,250'],
            'sinais_vitais.temperatura' => ['required', 'numeric', 'between:30.0,45.0'],
            'sinais_vitais.pressao_sistolica' => ['nullable', 'integer', 'between:40,260'],
            'sinais_vitais.pressao_diastolica' => ['nullable', 'integer', 'between:20,160'],
            'sinais_vitais.peso' => ['nullable', 'numeric', 'between:1.0,300.0'],
            'sinais_vitais.altura' => ['nullable', 'numeric', 'between:0.3,2.5'],
        ];
    }
}

?>