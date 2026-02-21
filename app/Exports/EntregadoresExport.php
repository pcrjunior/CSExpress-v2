<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class EntregadoresExport implements FromArray, WithHeadings, WithStyles, WithColumnFormatting, ShouldAutoSize, WithTitle
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
   public function array(): array
    {
        $query = DB::table('ordem_servico_ajudante as eos')
            ->join('entregadores as e', 'e.id', '=', 'eos.ajudante_id')
            ->join('ordem_servicos as os', 'os.id', '=', 'eos.ordem_servico_id')
            ->leftJoin('contas_pagar as cp', function($join) {
                $join->on('cp.ordem_servico_id', '=', 'os.id')
                    ->where('cp.status_pagamento', '=', 'pendente');
            })
            ->whereNull('e.deleted_at')
            ->whereNull('os.deleted_at')
            ->select(
                'e.id',
                'e.nome',
                DB::raw('COUNT(os.id) as total_ordens'),
                DB::raw('SUM(eos.valor) as total_pago'),
                DB::raw('SUM(cp.valor_total) as total_pendente')
            )
            ->groupBy('e.id', 'e.nome')
            ->orderBy('e.nome');

        if ($this->request->filled('data_inicio') && $this->request->filled('data_fim')) {
            $query->whereBetween('os.data_servico', [
                $this->request->data_inicio,
                $this->request->data_fim
            ]);
        }

        if ($this->request->filled('entregador_id')) {
            $query->where('e.id', $this->request->entregador_id);
        }

        $dados = $query->get();

        return $dados->map(function ($linha) {
            return [
                $linha->nome,
                $linha->total_ordens,
                (float) ($linha->total_pago ?? 0),
                (float) ($linha->total_pendente ?? 0),
            ];
        })->toArray();
    }


    public function headings(): array
    {
        return ['Nome', 'Total de Ordens', 'Total Recebido (R$)', 'Total Pendente (R$)'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]], // Primeira linha (cabeçalhos)
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0.00' ,
            'D' => '#,##0.00' ,
        ];
    }

    public function title(): string
    {
        return 'Entregadores';
    }
}
