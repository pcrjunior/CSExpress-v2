<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Clientes Atendidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .logo {
            height: 40px;
        }

        .titulo {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            flex: 1;
            margin-left: -40px;
        }

        .data {
            font-size: 10px;
            color: #666;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
            padding: 6px;
            border: 1px solid #ccc;
            border-collapse: collapse;
            text-align: left;
        }

        td {
            padding: 5px 6px;
            border: 1px solid #ccc;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        footer {
            position: fixed;
            bottom: 15px;
            left: 30px;
            right: 30px;
            font-size: 9px;
            color: #888;
            text-align: center;
        }

        .page-number:after {
            content: "Página " counter(page);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
           font-weight: bold;
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
                    Relatório de Clientes Atendidos
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
                <th style="text-align: left;">Cliente</th>
                <th style="text-align: center;">Total de Ordens</th>
                <th style="text-align: right;">Valor Total (R$)</th>
                <th style="text-align: center;">Última O.S.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dados as $item)
                <tr>
                    <td>{{ $item['nome'] }}</td>
                    <td class="text-center">{{ $item['total_os'] }}</td>
                    <td class="text-right">R$ {{ number_format($item['valor_total'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ \Carbon\Carbon::parse($item['ultima_os'])->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Nenhum cliente encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer>
        <span class="page-number"></span> &nbsp;|&nbsp; CS Express © {{ now()->year }}
    </footer>
</body>
</html>
