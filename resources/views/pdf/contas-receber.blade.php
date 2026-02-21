<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Contas a Receber</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { color: #2c3e50; margin-bottom: 10px; }
        p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .badge { padding: 3px 6px; border-radius: 3px; color: #fff; font-size: 11px; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
    </style>
</head>
<body>
    <h2>Relatório de Contas a Receber</h2>
    <p>Total de registros: {{ $contas->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th class="text-end">Valor (R$)</th>
                <th>Vencimento</th>
                <th>Recebimento</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contas as $conta)
            <tr>
                <td>{{ $conta->id }}</td>
                <td>{{ optional($conta->ordemServico->clienteContratante)->nome ?? '-' }}</td>
                <td class="text-end">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                <td>{{ $conta->data_vencimento ? \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') : '-' }}</td>
                <td>{{ $conta->data_recebimento ? \Carbon\Carbon::parse($conta->data_recebimento)->format('d/m/Y') : '-' }}</td>
                <td>
                    <span class="badge {{ $conta->status_pagamento === 'pago' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($conta->status_pagamento) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
