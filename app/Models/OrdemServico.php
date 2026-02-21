<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OrdemServicoHistorico;

class OrdemServico extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ordem_servicos';

    protected $fillable = [
        'numero_os',
        'empresa_id',
        'cliente_origem_id',
        'cliente_destino_id',
        'contratante_tipo',
        'motorista_id',
        'veiculo_id',
        'user_id',
        'data_servico',
        'hora_inicial',
        'hora_final',
        'tempo_total',
        'valor_ajudantes',
        'valor_motorista',
        'valor_perimetro',
        'valor_restricao',
        'valor_total',
        'observacoes',
        'status',
        'created_by',
        'valor_repassado_motorista',
        'valor_repassado_ajudantes',
        'valor_repasse_resultado'
    ];

    protected $casts = [
        'data_servico' => 'date',
        'valor_ajudantes' => 'decimal:2',
        'valor_motorista' => 'decimal:2',
        'valor_perimetro' => 'decimal:2',
        'valor_restricao' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'valor_repassado_ajudantes' => 'decimal:2',
        'valor_repassado_motorista' => 'decimal:2',
        'valor_repasse_resultado' => 'decimal:2'
    ];

    const STATUS = [
        'pendente' => 'Pendente',
        'em_andamento' => 'Em Andamento',
        'concluido' => 'Concluído',
        'cancelado' => 'Cancelado'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->numero_os)) {
                $model->numero_os = self::gerarNumeroOS();
            }
        });

        static::created(function ($model) {
            $model->historicos()->create([
                'user_id' => $model->user_id,
                'status_anterior' => null,
                'status_novo' => $model->status ?? 'pendente',
                'observacao' => 'Ordem de serviço criada',
            ]);
        });

        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                $model->historicos()->create([
                    'user_id' => auth()->id() ?? $model->user_id,
                    'status_anterior' => $model->getOriginal('status'),
                    'status_novo' => $model->status,
                    'observacao' => 'Status atualizado',
                ]);
            }
        });
    }

   /*  public static function gerarNumeroOS(): string
    {
        return DB::transaction(function () {
            $prefixo = date('Ymd');

            $ultimaOS = self::withTrashed()
                ->where('numero_os', 'like', "$prefixo%")
                ->lockForUpdate()
                ->orderBy('numero_os', 'desc')
                ->first();

            $numero = $ultimaOS
                ? (int) Str::substr($ultimaOS->numero_os, -6) + 1
                : 1;

            return "$prefixo" . str_pad($numero, 6, '0', STR_PAD_LEFT);
        });
    } */

    public static function gerarNumeroOS(): string
    {
        return DB::transaction(function () {
            $ultimaOS = self::withTrashed()
                ->whereRaw('LEN(numero_os) = 7') // Corrigido para SQL Server
                ->lockForUpdate()
                ->orderBy('numero_os', 'desc')
                ->first();

            $numero = $ultimaOS
                ? (int) $ultimaOS->numero_os + 1
                : 7000;

            return str_pad($numero, 7, '0', STR_PAD_LEFT);
        });
    }



    public function calcularTempoTotal(): ?string
    {
        if (!$this->hora_inicial || !$this->hora_final) return null;

        $inicio = \Carbon\Carbon::parse($this->hora_inicial);
        $fim = \Carbon\Carbon::parse($this->hora_final);

        if ($fim->lt($inicio)) $fim->addDay();

        $diferenca = $fim->diff($inicio);
        return sprintf('%02d:%02d', $diferenca->h, $diferenca->i);
    }

    public function empresa(): BelongsTo { return $this->belongsTo(Empresa::class); }
    public function clienteOrigem(): BelongsTo { return $this->belongsTo(Cliente::class, 'cliente_origem_id'); }
    public function clienteDestino(): BelongsTo { return $this->belongsTo(Cliente::class, 'cliente_destino_id'); }
    public function motorista(): BelongsTo { return $this->belongsTo(Entregador::class, 'motorista_id')->where('perfil', 'Motorista'); }
    public function veiculo(): BelongsTo { return $this->belongsTo(Veiculo::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }

    public function ajudantes(): BelongsToMany
    {
        return $this->belongsToMany(Entregador::class, 'ordem_servico_ajudante', 'ordem_servico_id', 'ajudante_id')
            ->where('perfil', 'Ajudante')
            ->withPivot('valor', 'valor_repassado_ajudante')
            ->withTimestamps();
    }

    public function historicos(): HasMany { return $this->hasMany(OrdemServicoHistorico::class); }

    public function scopeEmpresa($query, $empresaId) {
        return $query->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId));
    }

    public function scopeFiltrarPorCliente($query, $clienteId)
    {
        return $query->when($clienteId, fn($q) => $q->where(function ($sub) use ($clienteId) {
            $sub->where('cliente_origem_id', $clienteId)->orWhere('cliente_destino_id', $clienteId);
        }));
    }

    public function scopePeriodo($query, $inicio, $fim)
    {
        return $query->when($inicio, fn($q) => $q->whereDate('data_servico', '>=', $inicio))
                     ->when($fim, fn($q) => $q->whereDate('data_servico', '<=', $fim));
    }

    public function scopeMotorista($query, $motoristaId) {
        return $query->when($motoristaId, fn($q) => $q->where('motorista_id', $motoristaId));
    }

    public function scopeStatus($query, $status) {
        return $query->when($status, fn($q) => $q->where('status', $status));
    }

    public function scopeNumeroOS($query, $numeroOS) {
        return $query->when($numeroOS, fn($q) => $q->where('numero_os', 'like', "%$numeroOS%"));
    }

    public function contasPagar(): HasMany { return $this->hasMany(ContaPagar::class, 'ordem_servico_id'); }
    public function contasReceber(): HasMany { return $this->hasMany(ContaReceber::class, 'ordem_servico_id'); }

    public function getTotalPagarAttribute(): float
    {
        $valorMotorista = $this->valor_motorista ?? 0;
        $valorAjudantes = $this->ajudantes->sum('pivot.valor') ?? 0;
        return $valorMotorista + $valorAjudantes;
    }

    public function getTemPendenciaFinanceiraAttribute(): bool
    {
        return $this->contasPagar()->where('status_pagamento', 'pendente')->exists()
            || $this->contasReceber()->where('status_pagamento', 'pendente')->exists();
    }

    public function getSaldoAttribute(): float
    {
        return ($this->valor_total ?? 0) - $this->getTotalPagarAttribute();
    }

    public function scopePagamentosPendentes($query)
    {
        return $query->whereHas('contasPagar', fn($q) => $q->where('status_pagamento', 'pendente'));
    }

    public function scopeRecebimentosPendentes($query)
    {
        return $query->whereHas('contasReceber', fn($q) => $q->where('status_pagamento', 'pendente'));
    }

    public function getResultadoFormatadoAttribute() {
        return number_format($this->resultado, 2, ',', '.');
    }

    public function getClienteAttribute() {
        return $this->clienteOrigem ?? $this->clienteDestino;
    }

    public function entregador() { return $this->belongsTo(Entregador::class, 'entregador_id'); }
    public function colaborador(): BelongsTo { return $this->belongsTo(Entregador::class, 'entregador_id'); }

    /* public function clienteContratante()
    {
        return $this->belongsTo(Cliente::class, $this->contratante_tipo === 'origem' ? 'cliente_origem_id' : 'cliente_destino_id');
    } */

    public function getClienteContratanteAttribute()
    {
        return $this->contratante_tipo === 'origem' ? $this->clienteOrigem : $this->clienteDestino;
    }
    public function contaReceber()
    {
        return $this->hasOne(\App\Models\ContaReceber::class, 'ordem_servico_id');
    }

}
