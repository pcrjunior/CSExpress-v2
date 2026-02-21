<?php

namespace App\Exports;

use App\Models\ContaPagar;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ContasPagarExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $query = ContaPagar::with(['ordemServico.clienteOrigem', 'entregador']);

        // Filtros de data
        if ($this->request->filled('data_vencimento_inicio')) {
            $query->where('data_vencimento', '>=', $this->request->data_vencimento_inicio);
        }

        if ($this->request->filled('data_vencimento_fim')) {
            $query->where('data_vencimento', '<=', $this->request->data_vencimento_fim);
        }

        if ($this->request->filled('data_pagamento_inicio')) {
            $query->where('data_pagamento', '>=', $this->request->data_pagamento_inicio);
        }

        if ($this->request->filled('data_pagamento_fim')) {
            $query->where('data_pagamento', '<=', $this->request->data_pagamento_fim);
        }

        if ($this->request->filled('cliente_id')) {
            $query->whereHas('ordemServico', function ($q) {
                $q->where('cliente_origem_id', $this->request->cliente_id);
            });
        }

        // Retorna a coleção formatada
        return $query->get()->map(function ($conta) {
            return [
                'Motorista'        => $conta->entregador->nome ?? '-',
                'Data do Serviço'  => optional($conta->ordemServico?->data_servico)->format('d/m/Y') ?? '-',
                'Data Pagamento'   => optional($conta->data_pagamento)->format('d/m/Y') ?? '-',
                'Cliente'          => $conta->ordemServico->clienteOrigem->nome ?? '-',
                'Valor (R$)'       => number_format($conta->valor_total, 2, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Motorista',
            'Data do Serviço',
            'Data do Pagamento',
            'Cliente - Contratante',
            'Valor (R$)'
        ];
    }
}
