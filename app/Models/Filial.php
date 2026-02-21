<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Filial extends Model
{
    use SoftDeletes; // Usa o trait de soft deletes

    protected $table = 'filiais';

    protected $fillable = [
        'nome', 'cnpj', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'estado', 'cep', 'empresa_id'
    ];

    protected $dates = ['deleted_at'];
}