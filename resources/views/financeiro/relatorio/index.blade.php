@extends('financeiro.layouts.financeiro')

@section('content')
    @component('financeiro.layouts.financeiro')
        @slot('pageTitle', 'Relatórios Financeiros')
        @slot('icon', 'chart-line')
        
        @slot('actions')
            <a href="{{ route('financeiro.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        @endslot
        
        @component('financeiro.components.section-card', ['title' => 'Filtrar Relatório', 'icon' => 'filter'])
            <form action="{{ route('financeiro.gerar-relatorio') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" required value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-5">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" required value="{{ request('data_fim') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Gerar Relatório
                    </button>
                </div>
            </form>
        @endcomponent
        
        @component('financeiro.components.section-card', ['title' => 'Tipos de Relatórios Disponíveis', 'icon' => 'file-alt'])
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-money-bill-wave me-2"></i> Fluxo de Caixa</h5>
                            <p class="card-text">Visualize entradas e saídas de recursos no período selecionado.</p>
                            <p><small class="text-muted">Inclui: Recebimentos, pagamentos e saldo.</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-chart-pie me-2"></i> Lucratividade</h5>
                            <p class="card-text">Análise de lucros por tipo de serviço e cliente.</p>
                            <p><small class="text-muted">Inclui: Margem de lucro, lucro bruto e lucro líquido.</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-user-clock me-2"></i> Produtividade</h5>
                            <p class="card-text">Desempenho de motoristas e ajudantes.</p>
                            <p><small class="text-muted">Inclui: OS por entregador, tempo médio e avaliações.</small></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-exclamation-triangle me-2"></i> Inadimplência</h5>
                            <p class="card-text">Relatório de contas a receber vencidas.</p>
                            <p><small class="text-muted">Inclui: Taxa de inadimplência, valores em atraso e ranking de clientes.</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i> Projeção</h5>
                            <p class="card-text">Projeção de receitas e despesas futuras.</p>
                            <p><small class="text-muted">Inclui: Projeção de caixa, previsão de recebimentos e pagamentos.</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-users me-2"></i> Clientes</h5>
                            <p class="card-text">Análise de rentabilidade por cliente.</p>
                            <p><small class="text-muted">Inclui: Ranking de clientes, frequência de serviços e valores.</small></p>
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    @endcomponent
@endsection