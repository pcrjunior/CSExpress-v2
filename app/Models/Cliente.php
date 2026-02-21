<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipo',
        'documento',
        'nome',
        'apelido',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'responsavel',
        'telefone',
        'email',
        'responsavel2',
        'telefone2',
        'email2',
        'avulso'
    ];

    // Formatar CPF/CNPJ ao acessar
    public function getDocumentoFormatadoAttribute()
    {
        $doc = $this->documento;

        if ($this->tipo == 'PF') {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $doc);
        } else {
            return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $doc);
        }
    }

    // Remover formatação antes de salvar
    public function setDocumentoAttribute($value)
    {
        $this->attributes['documento'] = preg_replace('/[^0-9]/', '', $value);
    }

    // Formatar CEP ao acessar
    public function getCepFormatadoAttribute()
    {
        return preg_replace("/(\d{5})(\d{3})/", "\$1-\$2", $this->cep);
    }

    // Remover formatação antes de salvar
    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setApelidoAttribute($value)
    {
        $this->attributes['apelido'] = trim(preg_replace('/\s+/', ' ', $value)); // remove espaços duplicados
    }
}
