<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContaPagar extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    
    /**
     * Nome da tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'contas_pagar';
    
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'ordem_servico_id',
        'entregador_id',
        'tipo',
        'valor_total',
        'data_vencimento',
        'data_pagamento',
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
        'data_pagamento' => 'date',
        'valor_total' => 'decimal:2',
    ];
    
    /**
     * Obtém a ordem de serviço associada a esta conta a pagar.
     */
    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }
    
    /**
     * Obtém o entregador (motorista ou ajudante) associado a esta conta a pagar.
     */
    public function entregador()
    {
        return $this->belongsTo(Entregador::class, 'entregador_id');
    }

    
    /**
     * Escopo para filtrar apenas contas pendentes.
     */
    public function scopePendentes($query)
    {
        return $query->where('status_pagamento', 'pendente');
    }
    
    /**
     * Escopo para filtrar apenas contas pagas.
     */
    public function scopePagas($query)
    {
        return $query->where('status_pagamento', 'pago');
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

    public function ordemServicoAjudantes()
    {
        return $this->hasMany(OrdemServicoAjudante::class, 'ordem_servico_id', 'ordem_servico_id');
    }
  

}