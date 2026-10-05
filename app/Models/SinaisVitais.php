<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SinaisVitais extends Model
{
    use HasFactory;

    protected $table = 'sinais_vitais';

    protected $fillable = [
        'triagem_id',
        'spo2',
        'frequencia_cardiaca',
        'temperatura',
        'pressao_sistolica',
        'pressao_diastolica',
        'peso',
        'altura',
    ];

    protected function casts(): array
    {
        return [
            'spo2' => 'integer',
            'frequencia_cardiaca' => 'integer',
            'temperatura' => 'float',
            'pressao_sistolica' => 'integer',
            'pressao_diastolica' => 'integer',
            'peso' => 'float',
            'altura' => 'float',
        ];
    }

    public function triagem(): BelongsTo
    {
        return $this->belongsTo(Triagem::class);
    }
}
?>