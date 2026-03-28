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
            'Apelido',
            'Valor Motorista',
            'Valor Ajudante',
        ];
    }

    public function collection()
    {
        $query = OrdemServico::query()
            ->with(['motorista', 'ajudantes'])
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelado');

        $dataInicio = $this->request->input('data_inicio');
        $dataFim = $this->request->input('data_fim');

        if ($this->request->filled('data_inicio')) {
            $query->whereDate('data_servico', '>=', $dataInicio);
        }

        if ($this->request->filled('data_fim')) {
            $query->whereDate('data_servico', '<=', $dataFim);
        }

        if ($this->request->filled('motorista_id')) {
            $query->where('motorista_id', $this->request->motorista_id);
        }

        $ordens = $query->orderBy('motorista_id')->orderBy('data_servico')->get();

        // Agrupar por motorista
        $dadosAgrupados = [];
        $totalGeralMotorista = 0;
        $totalGeralAjudante = 0;

        foreach ($ordens as $os) {
            $motorista = $os->motorista;
            $nomeMotorista = $motorista->nome ?? '';
            $apelido = '';
            
            if (strpos($nomeMotorista, ' - ') !== false) {
                [$nomeMotorista, $apelido] = explode(' - ', $nomeMotorista, 2);
            }

            $motoristaId = $os->motorista_id;
            if (!isset($dadosAgrupados[$motoristaId])) {
                $dadosAgrupados[$motoristaId] = [
                    'nome_motorista' => $nomeMotorista,
                    'apelido_motorista' => $apelido,
                    'ordens' => [],
                    'total_motorista' => 0,
                    'total_ajudante' => 0,
                ];
            }

            $valorMotorista = (float) ($os->valor_motorista ?? 0);
            $valorAjudante = (float) ($os->valor_ajudantes ?? 0);

            $dadosAgrupados[$motoristaId]['ordens'][] = [
                'numero_os' => $os->numero_os ?? '',
                'data_servico' => $os->data_servico,
                'valor_motorista' => $valorMotorista,
                'valor_ajudante' => $valorAjudante,
            ];

            $dadosAgrupados[$motoristaId]['total_motorista'] += $valorMotorista;
            $dadosAgrupados[$motoristaId]['total_ajudante'] += $valorAjudante;
            $totalGeralMotorista += $valorMotorista;
            $totalGeralAjudante += $valorAjudante;
        }

        // Construir linhas para Excel com agrupamento
        $linhas = [];
        $primeiroGrupo = true;

        foreach ($dadosAgrupados as $motorista) {
            // Linha em branco entre grupos
            if (!$primeiroGrupo) {
                $linhas[] = [
                    'numero_os' => '',
                    'data_servico' => '',
                    'nome_motorista' => '',
                    'apelido_motorista' => '',
                    'valor_motorista' => '',
                    'valor_ajudante' => '',
                ];
            }

            // Cabeçalho do motorista
            $nomeCompleto = $motorista['nome_motorista'];
            if ($motorista['apelido_motorista']) {
                $nomeCompleto .= ' - ' . $motorista['apelido_motorista'];
            }
            $linhas[] = [
                'numero_os' => 'MOTORISTA: ' . $nomeCompleto,
                'data_servico' => '',
                'nome_motorista' => '',
                'apelido_motorista' => '',
                'valor_motorista' => '',
                'valor_ajudante' => '',
            ];

            // Período
            $periodo = 'Período: ';
            if ($dataInicio) {
                $periodo .= \Carbon\Carbon::parse($dataInicio)->format('d/m/Y');
            } else {
                $periodo .= '__/__/____';
            }
            $periodo .= ' até ';
            if ($dataFim) {
                $periodo .= \Carbon\Carbon::parse($dataFim)->format('d/m/Y');
            } else {
                $periodo .= '__/__/____';
            }
            $linhas[] = [
                'numero_os' => $periodo,
                'data_servico' => '',
                'nome_motorista' => '',
                'apelido_motorista' => '',
                'valor_motorista' => '',
                'valor_ajudante' => '',
            ];

            // Linhas de cabeçalho de coluna para cada grupo
            $linhas[] = [
                'numero_os' => 'Número OS',
                'data_servico' => 'Data do Serviço',
                'nome_motorista' => '',
                'apelido_motorista' => '',
                'valor_motorista' => 'Valor Motorista',
                'valor_ajudante' => 'Valor Ajudante',
            ];

            // Dados das ordens
            foreach ($motorista['ordens'] as $ordem) {
                $linhas[] = [
                    'numero_os' => $ordem['numero_os'],
                    'data_servico' => $ordem['data_servico'],
                    'nome_motorista' => '',
                    'apelido_motorista' => '',
                    'valor_motorista' => $ordem['valor_motorista'],
                    'valor_ajudante' => $ordem['valor_ajudante'],
                ];
            }

            // Subtotal do motorista
            $linhas[] = [
                'numero_os' => '',
                'data_servico' => 'SUBTOTAL',
                'nome_motorista' => '',
                'apelido_motorista' => '',
                'valor_motorista' => $motorista['total_motorista'],
                'valor_ajudante' => $motorista['total_ajudante'],
            ];

            $primeiroGrupo = false;
        }

        // Linha em branco
        $linhas[] = [
            'numero_os' => '',
            'data_servico' => '',
            'nome_motorista' => '',
            'apelido_motorista' => '',
            'valor_motorista' => '',
            'valor_ajudante' => '',
        ];

        // TOTAL GERAL
        $linhas[] = [
            'numero_os' => '',
            'data_servico' => 'TOTAL GERAL',
            'nome_motorista' => '',
            'apelido_motorista' => '',
            'valor_motorista' => $totalGeralMotorista,
            'valor_ajudante' => $totalGeralAjudante,
        ];

        return collect($linhas);
    }

    public function map($row): array
    {
        // se data_servico está vazia, não precisa formatar data
        if (empty($row['data_servico'])) {
            return [
                $row['numero_os'] ?? '',
                $row['data_servico'] ?? '',
                $row['nome_motorista'] ?? '',
                $row['apelido_motorista'] ?? '',
                $row['valor_motorista'] ?? '',
                $row['valor_ajudante'] ?? '',
            ];
        }

        // verificar se é uma data válida antes de tentar parseá-la
        $dataServico = $row['data_servico'];
        $dataParsed = null;
        
        try {
            // Verificar se é uma string que parece ser data (pode ser formatada com / ou -)
            if (is_string($dataServico) && (strpos($dataServico, '-') !== false || strpos($dataServico, '/') !== false)) {
                $dataParsed = Date::dateTimeToExcel(
                    \Carbon\Carbon::parse($dataServico)
                );
            } else {
                $dataParsed = $dataServico;
            }
        } catch (\Exception $e) {
            // Se não conseguir parsear, retorna como string
            $dataParsed = $dataServico;
        }

        // formatar a data
        return [
            $row['numero_os'] ?? '',
            $dataParsed,
            $row['nome_motorista'] ?? '',
            $row['apelido_motorista'] ?? '',
            $row['valor_motorista'] ?? '',
            $row['valor_ajudante'] ?? '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Data (coluna B) - FORMAT_DATE_DDMMYYYY
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            // Valor Motorista (coluna E)
            'E' => '"R$" #,##0.00',
            // Valor Ajudante (coluna F)
            'F' => '"R$" #,##0.00',
        ];
    }
}
