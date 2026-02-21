<?php

namespace App\Exports;

use App\Models\OrdemServico;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientesAtendidosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles

{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = OrdemServico::query()
            ->with(['clienteOrigem', 'clienteDestino'])
            ->whereNull('ordem_servicos.deleted_at');

        if ($this->request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $this->request->data_inicio);
        }

        if ($this->request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $this->request->data_fim);
        }

        if ($this->request->filled('cliente_id')) {
            $query->where(function ($q) {
                $q->where('cliente_origem_id', $this->request->cliente_id)
                  ->orWhere('cliente_destino_id', $this->request->cliente_id);
            });
        }

        $ordens = $query->get();

        $clientesAgrupados = [];

        foreach ($ordens as $os) {
            $contratanteTipo = $os->contratante_tipo; // origem ou destino

            if ($contratanteTipo === 'origem' && $os->clienteOrigem) {
                $cliente = $os->clienteOrigem;
            } elseif ($contratanteTipo === 'destino' && $os->clienteDestino) {
                $cliente = $os->clienteDestino;
            } else {
                continue;
            }

            $id = $cliente->id;
            if (!isset($clientesAgrupados[$id])) {
                $clientesAgrupados[$id] = [
                    'Cliente'      => $cliente->nome,
                    'Apelido'      => $cliente->apelido,
                    'Total de OS'  => 0,
                    'Valor Total'  => 0,
                    'Última OS'    => null,
                ];
            }

            $clientesAgrupados[$id]['Total de OS']++;
            $clientesAgrupados[$id]['Valor Total'] += $os->valor_total ?? 0;
            $clientesAgrupados[$id]['Última OS'] = max(
                $clientesAgrupados[$id]['Última OS'] ?? '1900-01-01',
                $os->data_servico
            );
        }

        return collect($clientesAgrupados)->map(function ($dados) {
            return [
                $dados['Cliente'],
                $dados['Apelido'],
                $dados['Total de OS'],
                $dados['Valor Total'],
                optional($dados['Última OS'])->format('d/m/Y')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Apelido',
            'Total de OS',
            'Valor Total (R$)',
            'Última OS'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_00, // Valor Total
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:E1000')->getFont()->setSize(9);
        return [];
    }
}
