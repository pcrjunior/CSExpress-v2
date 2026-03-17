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


class ClientesAnaliticoExport implements
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

            'Data',
            'Cliente',
            'Apelido',
            'Carro',
            'Ajudantes',

        ];
    }

    public function collection()
    {

        $query = OrdemServico::query()
            ->with(['clienteOrigem', 'clienteDestino'])
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelado')
            ->whereIn('contratante_tipo', ['origem', 'destino']);


        if ($this->request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $this->request->data_inicio);
        }

        if ($this->request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $this->request->data_fim);
        }


        if ($this->request->filled('cliente_id')) {

            $clienteId = $this->request->cliente_id;

            $query->where(function ($q) use ($clienteId) {

                $q->where(function ($sub) use ($clienteId) {
                    $sub->where('contratante_tipo', 'origem')
                        ->where('cliente_origem_id', $clienteId);
                })

                ->orWhere(function ($sub) use ($clienteId) {
                    $sub->where('contratante_tipo', 'destino')
                        ->where('cliente_destino_id', $clienteId);
                });

            });
        }


        $ordens = $query->orderBy('data_servico')->get();


        $totalCarro = 0;
        $totalAjudantes = 0;


        $linhas = $ordens->map(function ($os) use (&$totalCarro, &$totalAjudantes) {

            $cliente = $os->contratante_tipo === 'origem'
                ? $os->clienteOrigem
                : $os->clienteDestino;


            $carro = (float) $os->valor_motorista;
            $ajudantes = (float) $os->valor_ajudantes;


            $totalCarro += $carro;
            $totalAjudantes += $ajudantes;


            return [

                'data' => $os->data_servico,
                'cliente' => $cliente->nome ?? '',
                'apelido' => $cliente->apelido ?? '',
                'carro' => $carro,
                'ajudantes' => $ajudantes,

            ];
        });


        // linha em branco
        $linhas->push([
            'data' => '',
            'cliente' => '',
            'apelido' => '',
            'carro' => '',
            'ajudantes' => '',
        ]);


        // TOTAL
        $linhas->push([
            'data' => '',
            'cliente' => 'TOTAL',
            'apelido' => '',
            'carro' => $totalCarro,
            'ajudantes' => $totalAjudantes,
        ]);


        return collect($linhas);
    }


    public function map($row): array
    {

        // quando for linha TOTAL (array)
        if (is_array($row)) {

            if (empty($row['data'])) {

                return [

                    '',
                    $row['cliente'] ?? '',
                    $row['apelido'] ?? '',
                    $row['carro'] ?? '',
                    $row['ajudantes'] ?? '',

                ];
            }

            return [

                Date::dateTimeToExcel(
                    \Carbon\Carbon::parse($row['data'])
                ),

                $row['cliente'] ?? '',
                $row['apelido'] ?? '',
                $row['carro'] ?? '',
                $row['ajudantes'] ?? '',

            ];
        }


        // quando for OrdemServico

        $cliente = $row->contratante_tipo === 'origem'
            ? $row->clienteOrigem
            : $row->clienteDestino;


        return [

            Date::dateTimeToExcel(
                \Carbon\Carbon::parse($row->data_servico)
            ),

            $cliente->nome ?? '',

            $cliente->apelido ?? '',

            (float) $row->valor_motorista,

            (float) $row->valor_ajudantes,

        ];
    }


    public function columnFormats(): array
    {
        return [

            // Data
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,

            // Carro
            'D' => '"R$" #,##0.00',

            // Ajudantes
            'E' => '"R$" #,##0.00',

        ];
    }
}
