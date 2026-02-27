<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Ordens de Serviço</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 25px 30px 60px 30px;
            color: #333;
        }

        .logo {
            height: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        td {
            font-size: 8px;
            padding: 6px;
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

        .text-success {
            color: #28a745;
            font-weight: bold;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .numero-os {
            white-space: nowrap;
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
            margin-bottom: 20px;
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
                Relatório de Ordens de Serviços
            </td>
            <td style="width: 20%;" class="report-date">
                <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <!-- Tabela de dados -->
    <table>
    <thead>
        <tr>
            <th style="width: 11%;">OS</th>
            <th style="width: 9%;">Data</th>
            <th style="width: 9%;">Status</th>
            <th style="width: 14%;">Origem</th>
            <th style="width: 14%;">Destino</th>
            <th style="width: 14%;">Apelido</th>
            <th style="width: 15%;">Motorista</th>
            <th style="width: 7%;" class="text-end">Total (R$)</th>
        </tr>
    </thead>

    <tbody>
        @foreach($ordens as $os)
            <tr>
                <td class="numero-os">{{ $os->numero_os ?? '-' }}</td>

                <td>
                    {{ $os->data_servico
                        ? \Carbon\Carbon::parse($os->data_servico)->format('d/m/Y')
                        : '-'
                    }}
                </td>

                <td>
                    {{ $os->status
                        ? ucfirst(str_replace('_', ' ', $os->status))
                        : '-'
                    }}
                </td>

                <td>{{ optional($os->clienteOrigem)->nome ?? '-' }}</td>
                <td>{{ optional($os->clienteDestino)->nome ?? '-' }}</td>

                <td>
                    {{ optional($os->clienteOrigem)->apelido ?? '-' }}
                    /
                    {{ optional($os->clienteDestino)->apelido ?? '-' }}
                </td>

                <td>{{ optional($os->motorista)->nome ?? '-' }}</td>

                <td class="text-end">
                    R$ {{ number_format($os->valor_total ?? 0, 2, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr style="background-color:#f2f2f2;">
            <td colspan="7" class="text-end text-bold">
                TOTAL GERAL:
            </td>
            <td class="text-end text-bold">
                R$ {{ number_format($totalGeral ?? 0, 2, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>

    <!-- Rodapé -->
    <footer>
        Sistema de Gestão de OS · Gerado em {{ now()->format('d/m/Y H:i') }}
    </footer>

</body>
</html>
