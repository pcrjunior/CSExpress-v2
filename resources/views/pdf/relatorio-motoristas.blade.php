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

        h2 {
            text-align: center;
            margin-bottom: 10px;
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

    <h2>Relatório de Motoristas</h2>

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
