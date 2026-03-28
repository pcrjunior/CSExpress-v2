<?php

namespace App\Exports;

use App\Models\OrdemServico;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class MotoristasAnaliticoExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function headings(): array
    {
        return [
            'Número OS',
            'Data do Serviço',
                'Nome Motorista',
    public function collection()
    {
        $query = OrdemServico::query()
            ->with(['motorista', 'ajudantes'])
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelado');

        if ($this->request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $this->request->data_inicio);
        }

        if ($this->request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $this->request->data_fim);
        }

        if ($this->request->filled('motorista_id')) {
            $query->where('motorista_id', $this->request->motorista_id);
        }

        $ordens = $query->orderBy('data_servico')->get();

        $totalMotorista = 0;
        $totalAjudante = 0;

        $linhas = $ordens->map(function ($os) use (&$totalMotorista, &$totalAjudante) {

            $motorista = $os->motorista;
            $valorMotorista = (float) $os->valor_motorista;
            $valorAjudante = (float) $os->valor_ajudantes;

            $totalMotorista += $valorMotorista;
            $totalAjudante += $valorAjudante;

            return [
                'numero_os' => $os->numero_os ?? '',
                'data_servico' => $os->data_servico,
                'nome_motorista' => $motorista->nome ?? '',
                'valor_motorista' => $valorMotorista,
                'valor_ajudante' => $valorAjudante,
            ];
        });

        // linha em branco
        $linhas->push([
            'numero_os' => '',
            'data_servico' => '',
            'nome_motorista' => '',
            'valor_motorista' => '',
            'valor_ajudante' => '',
        ]);

        // TOTAL
        $linhas->push([
            'numero_os' => '',
            'data_servico' => '',
            'nome_motorista' => 'TOTAL',
            'valor_motorista' => $totalMotorista,
            'valor_ajudante' => $totalAjudante,
        ]);

        return collect($linhas);
    }

    public function map($row): array
    {
        // quando for linha TOTAL (array)
        if (is_array($row)) {

            if (empty($row['data_servico'])) {

                return [
                    '',
                    '',
                    $row['nome_motorista'] ?? '',
                    $row['valor_motorista'] ?? '',
                    $row['valor_ajudante'] ?? '',
                ];
            }

            return [
                $row['numero_os'] ?? '',
                Date::dateTimeToExcel(
                    \Carbon\Carbon::parse($row['data_servico'])
                ),
                $row['nome_motorista'] ?? '',
                $row['valor_motorista'] ?? '',
                $row['valor_ajudante'] ?? '',
            ];
        }

        // quando for OrdemServico
        $motorista = $row->motorista;

        return [
            $row->numero_os ?? '',
            Date::dateTimeToExcel(
                \Carbon\Carbon::parse($row->data_servico)
            ),
            $motorista->nome ?? '',
            (float) $row->valor_motorista,
            (float) $row->valor_ajudantes,
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Data
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            // Valor Motorista
            'D' => '"R$" #,##0.00',
            // Valor Ajudante
            'E' => '"R$" #,##0.00',
        ];
    }
}
