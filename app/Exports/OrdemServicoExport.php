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

class OrdemServicoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = OrdemServico::with(['clienteOrigem', 'clienteDestino', 'empresa', 'motorista', 'ajudantes']);

        // seus filtros continuam aqui...

        return $query->get()->map(function ($os) {
            return [
                'Nº OS'       => "'" . $os->numero_os, // 👈 força como texto no Excel
                'Data'        => optional($os->data_servico)->format('d/m/Y'),
                'Status'      => ucfirst(str_replace('_', ' ', $os->status)),
                'Origem'      => $os->clienteOrigem->nome ?? '-',
                'Destino'     => $os->clienteDestino->nome ?? '-',
                'Apelido'     => ($os->clienteOrigem->apelido ?? '-') . ' / ' . ($os->clienteDestino->apelido ?? '-'),
                'Motorista'   => $os->motorista->nome ?? '-',
                'Valor Total' => $os->valor_total ?? 0,
                'Resultado'   => $os->valor_repasse_resultado ?? 0,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nº OS',
            'Data',
            'Status',
            'Origem',
            'Destino',
            'Apelido',
            'Motorista',
            'Valor Total',
            'Resultado'
        ];
    }

    public function columnFormats(): array
    {
         return [
            'A' => NumberFormat::FORMAT_TEXT, // Coluna A como texto
            'H' => '#,##0.00', // Coluna "Valor Total"
            'I' => '#,##0.00', // Coluna "Resultado"
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Define tamanho da fonte para todas as células a partir da linha 2 até a 1000
        $sheet->getStyle('A2:I1000')->getFont()->setSize(9);

        return [];
    }
}
