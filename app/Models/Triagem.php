<?php
namespace App\Models;

use App\Enums\ClassificacaoRiscoEnum;
use App\Enums\StatusTriagemEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Triagem extends Model
{
    use HasFactory;

    protected $table = 'triagens';

    protected $fillable = [
        'unidade_id',
        'totem_id',
        'paciente_id',
        'senha_atendimento',
        'queixa_principal',
        'questionario_respostas',
        'classificacao_risco',
        'status',
        'data_atendimento',
    ];

    protected function casts(): array
    {
        return [
            'questionario_respostas' => 'array',
            'classificacao_risco' => ClassificacaoRiscoEnum::class,
            'status' => StatusTriagemEnum::class,
            'data_atendimento' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function totem(): BelongsTo
    {
        return $this->belongsTo(Totem::class);
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }

    public function sinaisVitais(): HasOne
    {
        return $this->hasOne(SinaisVitais::class);
    }

    public function analiseIa(): HasOne
    {
        return $this->hasOne(AnaliseIa::class);
    }
}
?>