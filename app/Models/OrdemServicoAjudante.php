<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServicoAjudante extends Model
{
    protected $table = 'ordem_servico_ajudante';

    protected $fillable = [
        'ordem_servico_id',
        'entregador_id',
        'valor',
    ];

    public function entregador()
    {
        return $this->belongsTo(Entregador::class, 'entregador_id');
    }

    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }
}
