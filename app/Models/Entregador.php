<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Entregador extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'entregadores';

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'telefone',
        'email',
        'cnh_numero',
        'cnh_validade',
        'cnh_categoria',
        'active',
        'foto',
        'perfil',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_nascimento' => 'date',
        'cnh_validade' => 'date',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Escopo para filtrar apenas motoristas.
     */
    public function scopeMotoristas($query)
    {
        return $query->where('perfil', 'Motorista');
    }

    /**
     * Escopo para filtrar apenas ajudantes.
     */
    public function scopeAjudantes($query)
    {
        return $query->where('perfil', 'Ajudante');
    }

    /**
     * Escopo para filtrar apenas registros ativos.
     */
    public function scopeAtivos($query)
    {
        return $query->where('active', true);
    }

    /**
     * Veículos que o entregador pode utilizar.
     */
    public function veiculos()
    {
        return $this->belongsToMany(Veiculo::class, 'entregador_veiculo')
                    ->withTimestamps();
    }

    /**
     * Retorna as ordens de serviço onde este entregador é o motorista.
     */
    public function ordensServicoComoMotorista(): HasMany
    {
        return $this->hasMany(OrdemServico::class, 'motorista_id')
                    ->when($this->perfil === 'motorista', function ($query) {
                        return $query;
                    }, function ($query) {
                        // Se não for motorista, retorna uma coleção vazia
                        return $query->whereRaw('1 = 0');
                    });
    }

    /**
     * Retorna as ordens de serviço onde este entregador é um ajudante.
     */
    public function ordensServicoComoAjudante(): BelongsToMany
    {
        return $this->belongsToMany(OrdemServico::class, 'ordem_servico_ajudante', 'ajudante_id', 'ordem_servico_id')
                    ->when($this->perfil === 'ajudante', function ($query) {
                        return $query;
                    }, function ($query) {
                        // Se não for ajudante, retorna uma coleção vazia
                        return $query->whereRaw('1 = 0');
                    })
                    ->withPivot('valor')
                    ->withTimestamps();
    }

    /**
     * Exemplo de accessor para calcular a validade da CNH
     */
    public function getDiasValidadeCnhAttribute()
    {
        if ($this->cnh_validade) {
            return now()->diffInDays($this->cnh_validade, false);
        }
        return null;
    }

    /**
     * Verifica se o entregador é um Motorista.
     */
    public function isMotorista()
    {
        return $this->perfil === 'motorista';
    }

    /**
     * Verifica se o entregador é ajudante.
     */
    public function isAjudante(): bool
    {
        return $this->perfil === 'ajudante';
    }

    /**
     * Retorna o endereço completo formatado.
     */
    public function getEnderecoCompletoAttribute(): string
    {
        $endereco = $this->endereco ?? '';
        
        if ($this->numero) {
            $endereco .= ', ' . $this->numero;
        }
        
        if ($this->complemento) {
            $endereco .= ' - ' . $this->complemento;
        }
        
        if ($this->bairro) {
            $endereco .= ', ' . $this->bairro;
        }
        
        if ($this->cidade && $this->estado) {
            $endereco .= ', ' . $this->cidade . '/' . $this->estado;
        }
        
        if ($this->cep) {
            $endereco .= ' - CEP: ' . $this->cep;
        }
        
        return $endereco;
    }

    public function getNomeLimpoAttribute()
    {
        return explode(' - ', $this->nome)[0];
    }
}