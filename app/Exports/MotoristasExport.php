<?php

namespace App\Exports;

use App\Models\OrdemServico;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class MotoristasExport implements FromArray, WithEvents, WithStyles, ShouldAutoSize
{
    protected $request;
    protected $dados;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->dados = $this->gerarDados();
    }

    public function array(): array
    {
        return $this->dados;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => false,'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->setCellValue('A1', 'Extrato de Pagamentos');
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Formata a coluna E (valores)
                $ultimaLinha = count($this->dados);
                $event->sheet->getStyle("E3:E{$ultimaLinha}")
                    ->getNumberFormat()->setFormatCode('R$ #,##0.00');
            },
        ];
    }

    protected function gerarDados(): array
    {
        $query = OrdemServico::with(['motorista', 'clienteOrigem'])
            ->whereNotNull('motorista_id');

        if ($this->request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $this->request->data_inicio);
        }

        if ($this->request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $this->request->data_fim);
        }

        if ($this->request->filled('motorista_id')) {
            $query->where('motorista_id', $this->request->motorista_id);
        }

        $ordens = $query->get();

        if ($ordens->isEmpty()) {
            return [['Sem dados para exibir']];
        }

        $dados = [['Extrato de Pagamentos']];

        $grupos = $ordens->groupBy('motorista_id');

        foreach ($grupos as $grupo) {
            $motorista = $grupo->first()->motorista->nome ?? '-';
            $dados[] = ['Motorista', $motorista];
            $dados[] = ['Data do Serviço', 'Data do Pagamento', 'Cliente - Contratante', '', 'Valor'];

            $total = 0;

            foreach ($grupo as $os) {
                $cliente = $os->clienteOrigem->nome ?? '-';
                $dataServico = optional($os->data_servico)->format('d/m/Y');
                $dataPagamento = optional($os->data_pagamento)->format('d/m/Y');
                $valor = (float) $os->valor_motorista;
                $total += $valor;

                $dados[] = [
                    $dataServico,
                    $dataPagamento,
                    $cliente,
                    '',
                    $valor
                ];
            }

            $dados[] = ['', '', '', 'Total', $total];
            $dados[] = [''];
        }

        return $dados;
    }
}
