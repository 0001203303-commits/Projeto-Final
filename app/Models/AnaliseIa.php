<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnaliseIa extends Model
{
    use HasFactory;

    protected $table = 'analises_ia';

    protected $fillable = [
        'triagem_id',
        'modelo_utilizado',
        'resumo_clinico',
        'alerta_coerencia',
        'sugestao_encaminhamento',
        'raw_response',
        'tempo_processamento_ms',
    ];

    protected function casts(): array
    {
        return [
            'raw_response' => 'array',
            'tempo_processamento_ms' => 'integer',
        ];
    }

    public function triagem(): BelongsTo
    {
        return $this->belongsTo(Triagem::class);
    }
}
?>