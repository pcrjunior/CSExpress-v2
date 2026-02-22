<?php

namespace App\Exports;

use App\Models\OrdemServico;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class OrdemServicoExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithColumnFormatting,
    WithStyles
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = OrdemServico::with([
            'clienteOrigem',
            'clienteDestino',
            'empresa',
            'motorista',
            'ajudantes'
        ]);

        // 🔎 Aplicar filtros iguais aos da tela

        if ($this->request->filled('data_inicio') && $this->request->filled('data_fim')) {
            $query->whereBetween('data_servico', [
                $this->request->data_inicio,
                $this->request->data_fim
            ]);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('motorista_id')) {
            $query->where('motorista_id', $this->request->motorista_id);
        }

        if ($this->request->filled('cliente_id')) {
            $query->where(function($q) {
                $q->where('cliente_origem_id', $this->request->cliente_id)
                  ->orWhere('cliente_destino_id', $this->request->cliente_id);
            });
        }

        if ($this->request->filled('somente_contratante')) {
            $clienteId = $this->request->cliente_id;

            $query->where(function($q) use ($clienteId) {
                $q->where(function($sub) use ($clienteId) {
                    $sub->where('cliente_origem_id', $clienteId)
                        ->where('contratante_tipo', 'origem');
                })->orWhere(function($sub) use ($clienteId) {
                    $sub->where('cliente_destino_id', $clienteId)
                        ->where('contratante_tipo', 'destino');
                });
            });
        }

        $ordens = $query->get();

        // 🔢 Cálculo dos totais
        $somaTotal = $ordens->sum('valor_total');
        $somaResultado = $ordens->sum('valor_repasse_resultado');

        $dados = $ordens->map(function ($os) {
            return [
                'Nº OS'       => "'" . $os->numero_os,
                'Data'        => optional($os->data_servico)->format('d/m/Y'),
                'Status'      => ucfirst(str_replace('_', ' ', $os->status)),
                'Origem'      => $os->clienteOrigem->nome ?? '-',
                'Destino'     => $os->clienteDestino->nome ?? '-',
                'Apelido'     => ($os->clienteOrigem->apelido ?? '-') . ' / ' . ($os->clienteDestino->apelido ?? '-'),
                'Motorista'   => $os->motorista->nome ?? '-',
                'Observações' => $os->observacoes ?? '-',
                'Valor Total' => $os->valor_total ?? 0,
                'Resultado'   => $os->valor_repasse_resultado ?? 0,
            ];
        });

        // ➕ Linha TOTAL GERAL
        $dados->push([
            'Nº OS'       => '',
            'Data'        => '',
            'Status'      => '',
            'Origem'      => '',
            'Destino'     => '',
            'Apelido'     => '',
            'Motorista'   => 'TOTAL GERAL:',
            'Observações' => '',
            'Valor Total' => $somaTotal,
            'Resultado'   => $somaResultado,
        ]);

        return $dados;
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
            'Observações',
            'Valor Total',
            'Resultado'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'I' => '#,##0.00',
            'J' => '#,##0.00',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Fonte padrão
        $sheet->getStyle('A2:J' . $highestRow)
              ->getFont()
              ->setSize(9);

        // Última linha em negrito (TOTAL)
        $sheet->getStyle('A' . $highestRow . ':J' . $highestRow)
              ->getFont()
              ->setBold(true);

        // Linha divisória acima do TOTAL
        $sheet->getStyle('A' . ($highestRow - 1) . ':J' . ($highestRow - 1))
              ->getBorders()
              ->getBottom()
              ->setBorderStyle(Border::BORDER_THIN);

        return [];
    }
}
