<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 10px 15px 40px 15px;
            color: #333;
        }

        .logo {
            height: 35px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #ccc;
            padding: 4px;
            text-align: left;
        }

        td {
            font-size: 8px;
            padding: 4px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }

        .text-end {
            text-align: right;
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            font-size: 10px;
            color: #666;
            text-align: right;
        }

        .header-table {
            margin-bottom: 3px;
            border-bottom: 1px solid #ccc;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .report-date {
            font-size: 10px;
            text-align: right;
            color: #666;
        }

        .motorista-section {
            margin-top: 2px;
            margin-bottom: 5px;
        }

        .motorista-header {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .periodo-info {
            font-size: 9px;
            margin-bottom: 2px;
            color: #555;
        }

        .total-section {
            margin-top: 5px;
            font-size: 9px;
            font-weight: bold;
        }

        </style>

    </head>

    <body>

        <!-- Cabeçalho -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    <img src="{{ public_path('images/ecs-logo.png') }}" class="logo">
                </td>
                <td style="width: 60%;" class="report-title">
                    Relatório do Motorista - Analítico
                </td>
                <td style="width: 20%;" class="report-date">
                    <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>

        @foreach($dadosAgrupados as $motorista)
        <div @if($loop->first) style="margin-top: 0;" @endif>
            <div class="motorista-section" @if($loop->first) style="margin-top: 0;" @endif>
                <div class="periodo-info">
                    <strong>Período:</strong> 
                    {{ $dataInicio ? \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') : '__/__/___' }} 
                    até 
                    {{ $dataFim ? \Carbon\Carbon::parse($dataFim)->format('d/m/Y') : '__/__/___' }}
                </div>
                <div class="motorista-header">
                    Nome: {{ $motorista['nome_motorista'] }}{{ $motorista['apelido_motorista'] ? ' - ' . $motorista['apelido_motorista'] : '' }}
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Número OS</th>
                        <th>Data do Serviço</th>
                        <th class="text-end">Valor Motorista</th>
                        <th class="text-end">Valor Ajudante</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($motorista['ordens'] as $ordem)
                    <tr>
                        <td>
                            {{ $ordem['numero_os'] }}
                        </td>
                        <td>
                            {{ $ordem['data_servico'] }}
                        </td>
                        <td class="text-end">
                            {{ $ordem['valor_motorista'] }}
                        </td>
                        <td class="text-end">
                            {{ $ordem['valor_ajudante'] }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="total-section">
                        <td colspan="2" class="text-end">
                            TOTAL MOTORISTA
                        </td>
                        <td class="text-end">
                            {{ $motorista['total_motorista_formatado'] }}
                        </td>
                        <td class="text-end">
                            {{ $motorista['total_ajudante_formatado'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endforeach

        @if(count($dadosAgrupados) > 1)
        <div style="margin-top: 20px; border-top: 2px solid #333; padding-top: 10px;">
            <table>
                <tbody>
                    <tr class="total-section">
                        <td colspan="2" class="text-end">
                            TOTAL GERAL
                        </td>
                        <td class="text-end">
                            {{ $totalGeralMotorista }}
                        </td>
                        <td class="text-end">
                            {{ $totalGeralAjudante }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

    </body>
</html>
