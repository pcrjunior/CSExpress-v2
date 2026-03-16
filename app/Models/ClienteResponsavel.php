<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ClienteResponsavel extends Model
{

    protected $table = 'cliente_responsaveis';

    protected $fillable = [
        'cliente_id',
        'nome',
        'telefone',
        'email'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
