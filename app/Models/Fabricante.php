<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // se for necessário

class Fabricante extends Model
{
    use HasFactory, SoftDeletes; // se usar soft deletes

    protected $fillable = [
        'nome',
        'apelido',
        'tipo',
        'active', // se estiver utilizando controle de status
    ];
}
