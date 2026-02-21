<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Contas a Pagar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .badge { padding: 2px 6px; border-radius: 3px; color: #fff; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
    </style>
</head>
<body>
    <h2>Relatório de Contas a Pagar</h2>
    <p>Total de registros: {{ $contas->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>Nº OS</th>
                <th>Cliente</th>
                <th>Vencimento</th>
                <th>Pagamento</th>
                <th class="text-end">Valor (R$)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contas as $conta)
            <tr>
                <td>{{ $conta->ordemServico->numero_os ?? '-' }}</td>
                <td>
                    {{ $conta->ordemServico->clienteOrigem->nome ??
                       ($conta->ordemServico->clienteDestino->nome ?? '-') }}
                </td>
                <td>{{ \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}</td>
                <td>
                    {{ $conta->data_pagamento ? \Carbon\Carbon::parse($conta->data_pagamento)->format('d/m/Y') : '-' }}
                </td>
                <td class="text-end">R$ {{ number_format($conta->valor_total, 2, ',', '.') }}</td>
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
