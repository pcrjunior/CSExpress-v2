<?php

namespace App\Exports;

use App\Models\OrdemServico;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MotoristasAnaliticoExport implements
    FromCollection,
    WithMapping,
    ShouldAutoSize
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
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
                'data_servico' => \Carbon\Carbon::parse($os->data_servico)->format('d/m/Y'),
                'valor_motorista' => 'R$ ' . number_format($valorMotorista, 2, ',', '.'),
                'valor_ajudante' => 'R$ ' . number_format($valorAjudante, 2, ',', '.'),
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
                'valor_motorista' => 'R$ ' . number_format($motorista['total_motorista'], 2, ',', '.'),
                'valor_ajudante' => 'R$ ' . number_format($motorista['total_ajudante'], 2, ',', '.'),
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
            'valor_motorista' => 'R$ ' . number_format($totalGeralMotorista, 2, ',', '.'),
            'valor_ajudante' => 'R$ ' . number_format($totalGeralAjudante, 2, ',', '.'),
        ];

        return collect($linhas);
    }

    public function map($row): array
    {
        // Todos os valores já estão formatados corretamente na collection()
        return [
            $row['numero_os'] ?? '',
            $row['data_servico'] ?? '',
            $row['nome_motorista'] ?? '',
            $row['apelido_motorista'] ?? '',
            $row['valor_motorista'] ?? '',
            $row['valor_ajudante'] ?? '',
        ];
    }
}
