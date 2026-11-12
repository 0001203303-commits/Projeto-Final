<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = ['chave', 'valor'];

    public static function obter(string $chave, ?string $padrao = null): ?string
    {
        return static::where('chave', $chave)->value('valor') ?? $padrao;
    }

    /** Passar null remove a configuração (volta ao valor do .env). */
    public static function definir(string $chave, ?string $valor): void
    {
        if ($valor === null) {
            static::where('chave', $chave)->delete();

            return;
        }

        static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
    }

    public static function obterCriptografado(string $chave): ?string
    {
        $valor = static::obter($chave);

        if (! $valor) {
            return null;
        }

        try {
            return Crypt::decryptString($valor);
        } catch (DecryptException) {
            return null;
        }
    }

    public static function definirCriptografado(string $chave, string $valor): void
    {
        static::definir($chave, Crypt::encryptString($valor));
    }
}