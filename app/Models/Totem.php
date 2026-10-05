<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Totem extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'totens';

    protected $fillable = [
        'unidade_id',
        'codigo_totem',
        'descricao',
        'ip_address',
        'ultimo_ping_at',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'ultimo_ping_at' => 'datetime',
        ];
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class);
    }
}
?>