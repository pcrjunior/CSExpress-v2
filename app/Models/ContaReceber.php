<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaReceber extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    /**
     * Nome da tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'contas_receber';
    
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'ordem_servico_id',
        'valor_total',
        'data_vencimento',
        'data_recebimento',
        'status_pagamento',
        'observacao',
    ];
    
    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'data_vencimento' => 'date',
        'data_recebimento' => 'date',
        'valor_total' => 'decimal:2',
    ];
    
    /**
     * Obtém a ordem de serviço associada a esta conta a receber.
     */
    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }
    
    /**
     * Escopo para filtrar apenas contas pendentes.
     */
    public function scopePendentes($query)
    {
        return $query->where('status_pagamento', 'pendente');
    }
    
    /**
     * Escopo para filtrar apenas contas recebidas.
     */
    public function scopeRecebidas($query)
    {
        return $query->where('status_pagamento', 'recebido');
    }
    
    /**
     * Verifica se a conta está vencida.
     */
    public function getVencidaAttribute()
    {
        return $this->status_pagamento == 'pendente' && $this->data_vencimento < now();
    }
    
    /**
     * Formata o valor total como moeda.
     */
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }
    
    /**
     * Calcula os dias de atraso para contas vencidas.
     */
    public function getDiasAtrasoAttribute()
    {
        if ($this->getVencidaAttribute()) {
            return now()->diffInDays($this->data_vencimento);
        }
        
        return 0;
    }

    public function clienteOrigem()
    {
        return $this->hasOneThrough(
            \App\Models\Cliente::class,
            \App\Models\OrdemServico::class,
            'id',                  // id em OrdemServico
            'id',                  // id em Cliente
            'ordem_servico_id',    // foreign key em ContaReceber
            'cliente_origem_id'    // foreign key em OrdemServico
        );
    }


    public function clienteDestino()
    {
        return $this->hasOneThrough(
            \App\Models\Cliente::class,
            \App\Models\OrdemServico::class,
            'id',
            'id',
            'ordem_servico_id',
            'cliente_destino_id'
        );
    }
   




}