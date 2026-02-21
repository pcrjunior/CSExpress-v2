<?php

namespace App\Exports;

use App\Models\ContaReceber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ContasReceberExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = ContaReceber::with([
            'ordemServico',
            'ordemServico.clienteOrigem',
            'ordemServico.clienteDestino'
        ]);

        if ($this->request->filled('cliente_id')) {
            $query->whereHas('ordemServico', function ($q) {
                $q->where('cliente_origem_id', $this->request->cliente_id)
                ->orWhere('cliente_destino_id', $this->request->cliente_id);
            });
        }

        if ($this->request->filled('vencimento_de')) {
            $query->where('data_vencimento', '>=', $this->request->vencimento_de);
        }
        if ($this->request->filled('vencimento_ate')) {
            $query->where('data_vencimento', '<=', $this->request->vencimento_ate);
        }
        if ($this->request->filled('recebimento_de')) {
            $query->where('data_recebimento', '>=', $this->request->recebimento_de);
        }
        if ($this->request->filled('recebimento_ate')) {
            $query->where('data_recebimento', '<=', $this->request->recebimento_ate);
        }

        return $query->get()->map(function ($conta) {
            return [
                'Data do Serviço'    => optional($conta->ordemServico?->data_servico)->format('d/m/Y') ?? '-',
                'Cliente'           => optional($conta->ordemServico->clienteContratante)->nome ?? '-',
                'Apelido'            => optional($conta->ordemServico->clienteContratante)->apelido ?? '-',
                'Valor'             => number_format($conta->valor_total, 2, ',', '.'),
                'Data Recebimento'  => optional($conta->data_recebimento)->format('d/m/Y'),
                'Status'            => ucfirst($conta->status_pagamento),
            ];
        });
    }


    public function headings(): array
    {
        return ['Data do Serviço', 'Cliente', 'Apelido', 'Valor', 'Data Recebimento', 'Status'];
    }
}
