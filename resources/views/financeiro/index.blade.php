@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-2">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Dashboard Financeiro</h5>
                </div>
                
            </div>
        </div>
    </div>    
        
    <div class="row">
        <div class="col-md-12 mb-2">
            @component('financeiro.components.card-dashboard', ['title' => 'Ações Financeiras', 'icon' => 'cogs'])
                <div class="d-flex flex-wrap">
                    <a href="{{ route('financeiro.contas-pagar') }}" class="btn btn-primary m-2">
                        <i class="fas fa-money-bill-wave me-2"></i> Contas a Pagar
                    </a>
                    <a href="{{ route('financeiro.contas-receber') }}" class="btn btn-success m-2">
                        <i class="fas fa-hand-holding-usd me-2"></i> Contas a Receber
                    </a>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <!-- Resumo de saldos -->
        <div class="col-md-6 mb-4 ">
            @component('financeiro.components.card-dashboard', ['title' => 'Resumo de Saldos', 'icon' => 'wallet'])
                <div class="row">
                    @php
                        $tituloSaldoLiberado = 'Saldo Liberado <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Saldo já disponível, baseado em ordens de serviço concluídas e recebimentos efetivados."></i>';
                    @endphp
                    
                    <div class="col-md-6">
                        @include('financeiro.components.saldo-card', [
                            'bgClass' => 'bg-success text-white',
                            'title' => $tituloSaldoLiberado,
                            'valor' => $saldoLiberado,
                            'descricao' => 'OS concluídas'
                        ])
                    </div>
                    @php
                        $tooltip = 'Valor estimado com base nas ordens de serviço pendentes: total a receber menos total a pagar.';
                        $tituloSaldoFuturo = 'Saldo Futuro <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="' . $tooltip . '"></i>';
                    @endphp
                    <div class="col-md-6">
                        @include('financeiro.components.saldo-card', [
                            'bgClass' => 'bg-info text-white',
                            'title' => $tituloSaldoFuturo,
                            'valor' => $saldoFuturo,
                            'descricao' => 'OS pendentes'
                        ])
                    </div>
                </div>
            @endcomponent
        </div>
        
        <!-- Valores a receber/pagar -->
        <div class="col-md-6 mb-4">
            @component('financeiro.components.card-dashboard', ['title' => 'Resumo de Valores', 'icon' => 'money-bill-wave'])
                <div class="row">
                    @php
                        $tituloTotalReceber = 'Total a Receber <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Total a receber com base em ordens de serviço concluídas. O valor pendente representa OS concluídas que ainda não foram pagas."></i>';
                    @endphp
                    <div class="col-md-6">
                        @include('financeiro.components.saldo-card', [
                            'bgClass' => 'border-success',
                            'title' => $tituloTotalReceber,
                            'valor' => $totalReceberLiberado,
                            'descricao' => "Pendente: R$ " . number_format($recebimentosPendentes, 2, ',', '.')
                        ])
                    </div>
                    @php
                        $tituloTotalPagar = 'Total a Pagar <i class="bi bi-info-circle" data-bs-toggle="tooltip" title="Total a pagar com base em ordens de serviço concluídas. O valor pendente representa despesas registradas que ainda não foram pagas."></i>';
                    @endphp
                    <div class="col-md-6">
                        @include('financeiro.components.saldo-card', [
                            'bgClass' => 'border-danger',
                            'title' => $tituloTotalPagar,
                            'valor' => $totalPagarLiberado,
                            'descricao' => "Pendente: R$ " . number_format($pagamentosPendentes, 2, ',', '.')
                        ])
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
