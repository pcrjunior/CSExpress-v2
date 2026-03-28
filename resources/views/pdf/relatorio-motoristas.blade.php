<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Motoristas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        .logo {
            height: 40px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
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

        p {
            margin: 0;
            font-size: 12px;
        }

        .periodo {
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        footer {
            margin-top: 40px;
            font-size: 11px;
            text-align: right;
            color: #777;
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
                Relatório de Motoristas
            </td>
            <td style="width: 20%;" class="report-date">
                <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

    <div class="periodo">
        <p><strong>Período:</strong>
            {{ $data_inicio ? \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') : '---' }}
            até
            {{ $data_fim ? \Carbon\Carbon::parse($data_fim)->format('d/m/Y') : '---' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Motorista</th>
                <th>Total de Ordens</th>
                <th class="text-end">Total Pago (R$)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dados as $motorista)
                <tr>
                    <td>{{ $motorista->nome ?? '-' }}</td>
                    <td>{{ $motorista->total_ordens ?? 0 }}</td>
                    <td class="text-end">R$ {{ number_format($motorista->total_pago ?? 0, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Nenhum dado encontrado para os filtros aplicados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        Sistema de Gestão de OS · Gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </footer>

</body>
</html>
