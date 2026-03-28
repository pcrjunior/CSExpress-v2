<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
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
                    Relatório Analítico de Motoristas
                </td>
                <td style="width: 20%;" class="report-date">
                    <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Número OS</th>
                    <th>Data do Serviço</th>
                    <th>Nome Motorista</th>
                    <th>Apelido</th>
                    <th class="text-end">Valor Motorista</th>
                    <th class="text-end">Valor Ajudante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dados as $d)
                <tr>
                    <td>
                        {{ $d['numero_os'] }}
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($d['data_servico'])->format('d/m/y') }}
                    </td>
                    <td>
                        {{ $d['nome_motorista'] }}
                    </td>
                    <td>
                        {{ $d['apelido_motorista'] }}
                    </td>

                    <td class="text-end">
                        R$ {{ number_format($d['valor_motorista'],2,',','.') }}
                    </td>

                    <td class="text-end">
                        R$ {{ number_format($d['valor_ajudante'],2,',','.') }}
                    </td>

                </tr>
                @endforeach
                <tr class="total">
                    <td colspan="4" class="text-end">
                        TOTAL
                    </td>
                    <td class="text-end">
                        R$ {{ number_format($totalMotorista,2,',','.') }}
                    </td>
                    <td class="text-end">
                        R$ {{ number_format($totalAjudante,2,',','.') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
