<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdemServicoHistorico extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'ordem_servico_historicos';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ordem_servico_id',
        'user_id',
        'status_anterior',
        'status_novo',
        'observacao',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtém a ordem de serviço relacionada.
     */
    public function ordemServico(): BelongsTo
    {
        return $this->belongsTo(OrdemServico::class);
    }

    /**
     * Obtém o usuário que fez a alteração.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Retorna a última data em que a OS mudou para o status informado.
     *
     * @param  int    $osId
     * @param  string $statusNovo
     * @return Carbon|null
     */
    public static function ultimaDataPorStatus(int $osId, string $statusNovo): ?Carbon
    {
        $registro = self::where('ordem_servico_id', $osId)
            ->where('status_novo', $statusNovo)
            ->orderByDesc('created_at')
            ->first();

        return $registro
            ? Carbon::parse($registro->created_at)
            : null;
    }
}
