<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'veiculos';

    protected $fillable = [
        'placa',
        'fabricante',
        'modelo',
        'ano_fabricacao',
        'ano_modelo',
        'categoria',
        'cor',
        'rodizio',
        'dia_rodizio'
    ];

    /**
     * Entregadores (Motoristas) que podem utilizar este veículo.
     */
    public function entregadores()
    {
        return $this->belongsToMany(Entregador::class, 'entregador_veiculo')
                    ->withTimestamps();
    }

    public function getDescricaoCompletaAttribute()
    {
        return "{$this->fabricante} - {$this->modelo} - {$this->placa}";
    }

}
