@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-chart-pie me-2"></i> Resultado do Relatório Financeiro</h1>
        
        <div>
            <a href="{{ route('financeiro.relatorio') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimir
            </button>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-calendar me-2"></i> Resumo do Período
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Período</h5>
                            <p class="text-muted mb-0">De: {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}</p>
                            <p class="text-muted mb-0">Até: {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Serviços</h5>
                            <p class="text-muted mb-0">Total OS: {{ $osCount }}</p>
                            <p class="text-muted mb-0">OS Concluídas: {{ $osConcluidas }}</p>
                            <p class="text-muted mb-0">Taxa de conclusão: {{ $osCount > 0 ? number_format(($osConcluidas / $osCount) * 100, 2) : 0 }}%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-3 bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Lucro Realizado</h5>
                            <h3>R$ {{ number_format($lucroRealizado, 2, ',', '.') }}</h3>
                            <p class="mb-0">Previsão: R$ {{ number_format($lucroPrevisto, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-hand-holding-usd me-2"></i> Contas a Receber
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3 border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">Recebido</h5>
                            <h3>R$ {{ number_format($totalRecebido, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3 border-warning">
                        <div class="card-body">
                            <h5 class="card-title text-warning">A Receber</h5>
                            <h3>R$ {{ number_format($totalPendentesReceber, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="progress" style="height: 30px;">
                        @php
                            $totalRecebiveis = $totalRecebido + $totalPendentesReceber;
                            $porcentagemRecebido = $totalRecebiveis > 0 ? ($totalRecebido / $totalRecebiveis) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $porcentagemRecebido }}%;" 
                            aria-valuenow="{{ $porcentagemRecebido }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($porcentagemRecebido, 1) }}%
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ 100 - $porcentagemRecebido }}%;" 
                            aria-valuenow="{{ 100 - $porcentagemRecebido }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format(100 - $porcentagemRecebido, 1) }}%
                        </div>
                    </div>
                    <p class="text-center mt-2">
                        <small class="text-muted">Porcentagem de valores já recebidos vs. pendentes</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-money-bill-wave me-2"></i> Contas a Pagar
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3 border-danger">
                        <div class="card-body">
                            <h5 class="card-title text-danger">Pago</h5>
                            <h3>R$ {{ number_format($totalPago, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3 border-warning">
                        <div class="card-body">
                            <h5 class="card-title text-warning">A Pagar</h5>
                            <h3>R$ {{ number_format($totalPendentesPagar, 2, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="progress" style="height: 30px;">
                        @php
                            $totalPagaveis = $totalPago + $totalPendentesPagar;
                            $porcentagemPago = $totalPagaveis > 0 ? ($totalPago / $totalPagaveis) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $porcentagemPago }}%;" 
                            aria-valuenow="{{ $porcentagemPago }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($porcentagemPago, 1) }}%
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ 100 - $porcentagemPago }}%;" 
                            aria-valuenow="{{ 100 - $porcentagemPago }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format(100 - $porcentagemPago, 1) }}%
                        </div>
                    </div>
                    <p class="text-center mt-2">
                        <small class="text-muted">Porcentagem de valores já pagos vs. pendentes</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-chart-line me-2"></i> Análise Financeira
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Análise</th>
                                <th class="text-end">Valores</th>
                                <th class="text-end">Porcentagem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total a Receber</td>
                                <td class="text-end">R$ {{ number_format($totalRecebido + $totalPendentesReceber, 2, ',', '.') }}</td>
                                <td class="text-end">100%</td>
                            </tr>
                            <tr>
                                <td>Total a Pagar</td>
                                <td class="text-end">R$ {{ number_format($totalPago + $totalPendentesPagar, 2, ',', '.') }}</td>
                                <td class="text-end">
                                    @php
                                        $totalRecebiveis = $totalRecebido + $totalPendentesReceber;
                                        $totalPagaveis = $totalPago + $totalPendentesPagar;
                                        $percentualCusto = $totalRecebiveis > 0 ? ($totalPagaveis / $totalRecebiveis) * 100 : 0;
                                    @endphp
                                    {{ number_format($percentualCusto, 2) }}%
                                </td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Margem de Lucro</strong></td>
                                <td class="text-end">
                                    <strong>R$ {{ number_format($lucroPrevisto, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format(100 - $percentualCusto, 2) }}%</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Valores Já Recebidos</td>
                                <td class="text-end">R$ {{ number_format($totalRecebido, 2, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ $totalRecebiveis > 0 ? number_format(($totalRecebido / $totalRecebiveis) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                            <tr>
                                <td>Valores Já Pagos</td>
                                <td class="text-end">R$ {{ number_format($totalPago, 2, ',', '.') }}</td>
                                <td class="text-end">
                                    {{ $totalPagaveis > 0 ? number_format(($totalPago / $totalPagaveis) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                            <tr class="table-info">
                                <td><strong>Lucro Já Realizado</strong></td>
                                <td class="text-end">
                                    <strong>R$ {{ number_format($lucroRealizado, 2, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong>{{ $lucroPrevisto > 0 ? number_format(($lucroRealizado / $lucroPrevisto) * 100, 2) : 0 }}%</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center text-muted mt-4 mb-5 print-footer">
        <p>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>CSExpress - Sistema de Gestão</strong></p>
    </div>
</div>

<style media="print">
    @page {
        size: portrait;
        margin: 1cm;
    }
    
    .btn, .actions, nav, footer, header {
        display: none !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
    
    .print-footer {
        position: fixed;
        bottom: 0;
        width: 100%;
    }
</style>
@endsection