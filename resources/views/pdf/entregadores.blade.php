<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Entregadores</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 30px;
        }

        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        .logo {
            height: 40px;
        }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            flex: 1;
        }

        .data {
            font-size: 10px;
            text-align: right;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
        }

        th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        td.text-right {
            text-align: right;
        }

        td.text-center {
            text-align: center;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <table width="100%" style="margin-bottom: 15px; border-bottom: 0px solid #ccc; padding-bottom: 5px;">
            <tr>
                <td style="width: 20%;">
                    <img src="{{ public_path('images/ecs-logo.png') }}" class="logo" style="height: 40px;">
                </td>
                <td style="width: 60%; text-align: center; font-weight: bold; font-size: 16px;">
                    Relatório de Entregadores
                </td>
                <td style="width: 20%; text-align: center; font-size: 10px; color: #666;">
                    <strong>Data:</strong> {{ $dataAtual }}
                </td>
            </tr>
        </table>

    </header>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Total de Ordens</th>
                <th>Total Recebido (R$)</th>
                <th>Total Pendente (R$)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados as $linha)
                <tr>
                    <td>{{ $linha->nome }}</td>
                    <td class="text-center">{{ $linha->total_ordens }}</td>
                    <td class="text-right">R$ {{ number_format($linha->total_pago ?? 0, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($linha->total_pendente ?? 0, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty">Nenhum resultado encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
