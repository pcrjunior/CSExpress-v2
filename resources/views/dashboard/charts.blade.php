@extends('layouts.app')

@section('title', 'Dashboard de Entregas - Análise de Desempenho')

@section('content')

<div class="container-fluid py-4">
    <!-- Header com Logo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold text-dark">
                        <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Operacional
                    </h4>
                    <p class="text-muted mb-0 small">Visão geral do desempenho</p>
                </div>
                <div class="logo-badge">
                    <img src="{{ asset('images/ecs-logo-removebg.png') }}"
                         alt="Logo ECS"
                         class="logo-img">
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Resumo Principal - Dados Gerais (NÃO FILTRADOS) -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="section-badge">
                <i class="bi bi-clipboard-data me-2"></i>Resumo Geral
                <span class="badge bg-secondary ms-2">Dados em tempo real</span>
            </div>
        </div>

        <!-- Ordem de Serviço -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-secondary mb-0 fw-semibold">Ordens de Serviço</h6>
                            <small class="text-muted">Status atual</small>
                        </div>
                        <div class="metric-icon bg-primary">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <a href="{{ route('ordemservicos.index', ['status' => 'pendente']) }}"
                               class="text-decoration-none d-block clickable-card">
                                <div class="text-center p-2 bg-warning bg-opacity-10 rounded-3">
                                    <h3 class="fw-bold text-warning mb-0">{{ $totalOSAberta }}</h3>
                                    <small class="text-muted d-block">Pendente</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('ordemservicos.index', ['status' => 'concluido']) }}"
                               class="text-decoration-none d-block clickable-card">
                                <div class="text-center p-2 bg-success bg-opacity-10 rounded-3">
                                    <h3 class="fw-bold text-success mb-0">{{ $totalOS }}</h3>
                                    <small class="text-muted d-block">Concluído</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    @php
                        $taxaConclusao = ($totalOS + $totalOSAberta) > 0
                            ? round(($totalOS / ($totalOS + $totalOSAberta)) * 100, 1)
                            : 0;
                    @endphp
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Conclusão</small>
                            <span class="badge badge-soft-{{ $taxaConclusao >= 80 ? 'success' : ($taxaConclusao >= 60 ? 'warning' : 'danger') }}">
                                {{ $taxaConclusao }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $taxaConclusao >= 80 ? 'success' : ($taxaConclusao >= 60 ? 'warning' : 'danger') }}"
                                 style="width: {{ $taxaConclusao }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-secondary mb-0 fw-semibold">Clientes</h6>
                            <small class="text-muted">Base ativa</small>
                        </div>
                        <div class="metric-icon bg-info">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <a href="{{ route('clientes.index') }}"
                               class="text-decoration-none d-block clickable-card">
                                <div class="text-center p-2 bg-primary bg-opacity-10 rounded-3">
                                    <h3 class="fw-bold text-primary mb-0">{{ $totalClientes }}</h3>
                                    <small class="text-muted d-block">Total</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('clientes.index', ['com_os_ativa' => '1']) }}"
                               class="text-decoration-none d-block clickable-card">
                                <div class="text-center p-2 bg-success bg-opacity-10 rounded-3">
                                    <h3 class="fw-bold text-success mb-0">{{ $clientesComOSAberta }}</h3>
                                    <small class="text-muted d-block">Ativos</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    @php
                        $taxaAtividade = $totalClientes > 0
                            ? round(($clientesComOSAberta / $totalClientes) * 100, 1)
                            : 0;
                    @endphp
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Atividade</small>
                            <span class="badge badge-soft-info">{{ $taxaAtividade }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $taxaAtividade }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colaboradores -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-secondary mb-0 fw-semibold">Colaboradores</h6>
                            <small class="text-muted">Equipe</small>
                        </div>
                        <div class="metric-icon bg-success">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-primary bg-opacity-10 rounded-3">
                                <h3 class="fw-bold text-primary mb-0">{{ $totalColaboradores }}</h3>
                                <small class="text-muted d-block">Total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-success bg-opacity-10 rounded-3">
                                <h3 class="fw-bold text-success mb-0">{{ $entregadoresComOSAberta }}</h3>
                                <small class="text-muted d-block">Em Campo</small>
                            </div>
                        </div>
                    </div>
                    @php
                        $taxaOcupacao = $totalColaboradores > 0
                            ? round(($entregadoresComOSAberta / $totalColaboradores) * 100, 1)
                            : 0;
                    @endphp
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Ocupação</small>
                            <span class="badge badge-soft-{{ $taxaOcupacao >= 70 ? 'success' : ($taxaOcupacao >= 50 ? 'warning' : 'danger') }}">
                                {{ $taxaOcupacao }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $taxaOcupacao >= 70 ? 'success' : ($taxaOcupacao >= 50 ? 'warning' : 'danger') }}"
                                 style="width: {{ $taxaOcupacao }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Veículos -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="text-secondary mb-0 fw-semibold">Veículos</h6>
                            <small class="text-muted">Frota</small>
                        </div>
                        <div class="metric-icon bg-warning">
                            <i class="bi bi-truck"></i>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <a href="{{ route('veiculos.index') }}"
                               class="text-decoration-none d-block clickable-card">
                                <div class="text-center p-2 bg-primary bg-opacity-10 rounded-3">
                                    <h3 class="fw-bold text-primary mb-0">{{ $totalVeiculos }}</h3>
                                    <small class="text-muted d-block">Total</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('veiculos.index', ['em_uso' => '1']) }}"
                               class="text-decoration-none d-block clickable-card">
                                <div class="text-center p-2 bg-success bg-opacity-10 rounded-3">
                                    <h3 class="fw-bold text-success mb-0">{{ $veiculosComOSAberta }}</h3>
                                    <small class="text-muted d-block">Em Uso</small>
                                </div>
                            </a>
                        </div>
                    </div>
                    @php
                        $taxaUtilizacao = $totalVeiculos > 0
                            ? round(($veiculosComOSAberta / $totalVeiculos) * 100, 1)
                            : 0;
                    @endphp
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Utilização</small>
                            <span class="badge badge-soft-{{ $taxaUtilizacao >= 70 ? 'success' : ($taxaUtilizacao >= 50 ? 'warning' : 'danger') }}">
                                {{ $taxaUtilizacao }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $taxaUtilizacao >= 70 ? 'success' : ($taxaUtilizacao >= 50 ? 'warning' : 'danger') }}"
                                 style="width: {{ $taxaUtilizacao }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Secundárias -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-card-icon bg-primary-subtle">
                    <i class="bi bi-speedometer2 text-primary"></i>
                </div>
                <div class="metric-card-content">
                    @php
                        $mediaOSPorColab = $totalColaboradores > 0
                            ? round(($totalOS + $totalOSAberta) / $totalColaboradores, 1)
                            : 0;
                    @endphp
                    <h3 class="metric-value">{{ $mediaOSPorColab }}</h3>
                    <p class="metric-label">OS/Colaborador</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-card-icon bg-success-subtle">
                    <i class="bi bi-graph-up-arrow text-success"></i>
                </div>
                <div class="metric-card-content">
                    @php
                        $mediaOSPorCliente = $totalClientes > 0
                            ? round(($totalOS + $totalOSAberta) / $totalClientes, 1)
                            : 0;
                    @endphp
                    <h3 class="metric-value">{{ $mediaOSPorCliente }}</h3>
                    <p class="metric-label">OS/Cliente</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-card-icon bg-warning-subtle">
                    <i class="bi bi-clock-history text-warning"></i>
                </div>
                <div class="metric-card-content">
                    @php
                        $totalGeralOS = $totalOS + $totalOSAberta;
                        $percentualPendente = $totalGeralOS > 0
                            ? round(($totalOSAberta / $totalGeralOS) * 100, 1)
                            : 0;
                    @endphp
                    <h3 class="metric-value">{{ $percentualPendente }}%</h3>
                    <p class="metric-label">Pendentes ({{ $totalOSAberta }})</p>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="metric-card">
                <div class="metric-card-icon bg-info-subtle">
                    <i class="bi bi-check-circle text-info"></i>
                </div>
                <div class="metric-card-content">
                    @php
                        $totalGeralOS = $totalOS + $totalOSAberta;
                    @endphp
                    <h3 class="metric-value">{{ $totalGeralOS }}</h3>
                    <p class="metric-label">Total Geral</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Gráficos com Filtro -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="section-badge">
                <i class="bi bi-bar-chart-line me-2"></i>Análise de Desempenho
                <span class="badge bg-primary ms-2">Dados filtráveis por período</span>
            </div>
        </div>
    </div>

    <!-- Filtro de Período - Compacto e Destacado -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('dashboard.charts') }}" id="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-auto">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-funnel-fill text-primary me-2 fs-5"></i>
                                    <h6 class="mb-0 fw-semibold">Filtrar Período</h6>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="row g-2 align-items-end">
                                    <!-- Data Inicial -->
                                    <div class="col-md-3">
                                        <label for="start_date" class="form-label text-secondary small mb-1">Data Inicial</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-calendar3 text-muted"></i>
                                            </span>
                                            <input type="date" name="start_date" id="start_date"
                                                class="form-control border-start-0"
                                                value="{{ $startDate }}">
                                        </div>
                                    </div>

                                    <!-- Data Final -->
                                    <div class="col-md-3">
                                        <label for="end_date" class="form-label text-secondary small mb-1">Data Final</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-calendar3 text-muted"></i>
                                            </span>
                                            <input type="date" name="end_date" id="end_date"
                                                class="form-control border-start-0"
                                                value="{{ $endDate }}">
                                        </div>
                                    </div>

                                    <!-- Botões -->
                                    <div class="col-md-3 d-flex gap-2">
                                        <!-- Botão Filtrar -->
                                        <button type="submit" id="aplicar-filter"
                                                class="btn btn-primary fw-bold text-white px-3"
                                                onclick="sessionStorage.setItem('scrollToChart', '1')"
                                                title="Aplicar filtros">
                                            <i class="fas fa-search me-1"></i> Filtrar
                                        </button>

                                        <!-- Botão Limpar -->
                                        <button type="button" id="reset-filter"
                                                class="btn btn-outline-secondary fw-semibold px-3"
                                                title="Limpar filtros">
                                            <i class="fas fa-eraser me-1"></i> Limpar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if($startDate || $endDate)
                            <div class="col-12">
                                <div class="alert alert-info alert-sm mb-0 py-2 px-3 d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>
                                        <strong>Período ativo:</strong>
                                        {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : '...' }}
                                        até
                                        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : '...' }}
                                    </small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-4">
        <!-- Gráfico de entregas -->
        <div class="col-12 col-xl-6" id="scroll-to-chart">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-bar-chart-line me-2 text-primary"></i>Quantidade de Entregas
                        </h6>
                        <small class="text-muted">OS criadas por dia</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 300px;">
                        <canvas id="entregasPorDiaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de OS criadas por status -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-pie-chart me-2 text-primary"></i>OS por Situação
                        </h6>
                        <small class="text-muted">Distribuição de status</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div class="chart-container" style="position: relative; max-width: 300px; max-height: 300px;">
                        <canvas id="osStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Top 10 Colaboradores -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-trophy me-2 text-primary"></i>Top 10 Colaboradores
                        </h6>
                        <small class="text-muted">Ranking de produtividade</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="topColaboradoresChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 Clientes -->
        <div class="col-12 col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-star me-2 text-primary"></i>Top 10 Clientes
                        </h6>
                        <small class="text-muted">Maiores contratantes</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="topClientesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nova Linha de Gráficos -->
    <div class="row g-3 mb-4">
        <!-- Gráfico 1: Usuário x Quantidade de OS -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-person-check me-2 text-primary"></i>Usuário × OS
                        </h6>
                        <small class="text-muted">Top 10 usuários criadores</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="usuarioOsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Dia da Semana com Mais Corridas -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-calendar-week me-2 text-success"></i>Corridas por Dia
                        </h6>
                        <small class="text-muted">Distribuição semanal</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="diaSemanaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico 3: Top 10 Veículos Mais Utilizados -->
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-truck me-2 text-warning"></i>Top 10 Veículos
                        </h6>
                        <small class="text-muted">Modelos mais utilizados</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Filtrado</span>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 350px;">
                        <canvas id="topVeiculosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden inputs -->
    <input type="hidden" id="status-labels" value='@json($entregadoresStatusLabels)'>
    <input type="hidden" id="status-data" value='@json($entregadoresStatusData)'>
</div>

<!-- CSS Customizado -->
<style>
/* Logo Badge - Opção 2 Implementada */
.logo-badge {
    background: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
    transition: all 0.3s ease;
}

.logo-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50px;
    padding: 2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    z-index: -1;
}

.logo-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.25);
}

.logo-img {
    max-width: 200px;
    height: auto;
    display: block;
}

/* Section Badge */
.section-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
}

/* Metric Icon */
.metric-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.metric-icon.bg-primary { background-color: rgba(78, 115, 223, 0.1); color: #4e73df; }
.metric-icon.bg-info { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }
.metric-icon.bg-success { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
.metric-icon.bg-warning { background-color: rgba(246, 194, 62, 0.1); color: #f6c23e; }

/* Metric Card - Novo design compacto */
.metric-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    height: 100%;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.metric-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.metric-card-content {
    flex: 1;
}

.metric-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1;
}

.metric-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin: 0.25rem 0 0;
    line-height: 1;
}

/* Badge Soft */
.badge-soft-success { background-color: rgba(28, 200, 138, 0.1); color: #1cc88a; }
.badge-soft-warning { background-color: rgba(246, 194, 62, 0.1); color: #f6c23e; }
.badge-soft-danger { background-color: rgba(231, 74, 59, 0.1); color: #e74a3b; }
.badge-soft-info { background-color: rgba(54, 185, 204, 0.1); color: #36b9cc; }

/* Hover Effects */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12) !important;
}

.clickable-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.clickable-card:hover {
    transform: scale(1.03);
}

.clickable-card:active {
    transform: scale(0.98);
}

/* Alert Small */
.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

/* Progress Bar */
.progress {
    border-radius: 1rem;
    background-color: rgba(0,0,0,0.05);
}

.progress-bar {
    border-radius: 1rem;
}

/* Chart Container */
.chart-container {
    position: relative;
    width: 100%;
}

/* Card Customization */
.card {
    border-radius: 12px;
}

.card-header {
    background: transparent;
}

/* Responsive */
@media (max-width: 576px) {
    .logo-img {
        max-width: 120px;
    }

    .metric-value {
        font-size: 1.5rem;
    }

    .metric-label {
        font-size: 0.7rem;
    }

    .section-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }

    h3 {
        font-size: 1.5rem !important;
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

/* Background Subtle Classes */
.bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1); }
.bg-success-subtle { background-color: rgba(25, 135, 84, 0.1); }
.bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1); }
.bg-info-subtle { background-color: rgba(13, 202, 240, 0.1); }
</style>

@endsection

@push('scripts')

<script src="{{ asset('js/npm/chart.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Configuração global do Chart.js
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    //  Gráfico de Pizza
    function renderPizzaChart(id, labels, data, colors) {
        const ctx = document.getElementById(id)?.getContext('2d');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 📊 Gráfico de Barras Verticais
    function renderBarChart(id, labels, data, color, label = '') {
        const ctx = document.getElementById(id)?.getContext('2d');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: color,
                    borderRadius: 8,
                    maxBarThickness: 60
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { display: true, drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: label !== '', labels: { font: { size: 12 } } },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 12 }
                    }
                }
            }
        });
    }

    // 📉 Gráfico de Barras Horizontais
    function renderHorizontalBarChart(id, labels, data, color, tooltipLabel = 'Registros') {
        const ctx = document.getElementById(id)?.getContext('2d');
        if (!ctx) return;
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: tooltipLabel,
                    data: data,
                    backgroundColor: color,
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.x} ${tooltipLabel}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { display: true, drawBorder: false }
                    },
                    y: {
                        ticks: {
                            autoSkip: false,
                            font: { size: window.innerWidth < 768 ? 9 : 10 }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Cores
    const colors = {
        primary: '#4e73df',
        success: '#1cc88a',
        warning: '#f6c23e',
        danger: '#e74a3b',
        info: '#36b9cc',
        secondary: '#858796'
    };

    // Renderizar gráficos
    renderPizzaChart('osStatusChart',
        {!! json_encode($osCriadasStatusLabels ?? []) !!},
        {!! json_encode($osCriadasStatusData ?? []) !!},
        [colors.primary, colors.success, colors.warning, colors.danger, colors.info, colors.secondary]
    );

    renderBarChart('entregasPorDiaChart',
        {!! json_encode($entregasPorDiaLabels ?? []) !!},
        {!! json_encode($entregasPorDiaData ?? []) !!},
        colors.primary, 'OS Criadas'
    );

    renderHorizontalBarChart('topColaboradoresChart',
        {!! json_encode($topColaboradoresLabels ?? []) !!},
        {!! json_encode($topColaboradoresData ?? []) !!},
        colors.info, 'OS por colaborador'
    );

    renderHorizontalBarChart('topClientesChart',
        {!! json_encode($topClientesLabels ?? []) !!},
        {!! json_encode($topClientesData ?? []) !!},
        colors.primary, 'OS contratadas'
    );

    if (sessionStorage.getItem('scrollToChart')) {
        const chartElement = document.getElementById('scroll-to-chart');
        if (chartElement) {
            setTimeout(() => {
                chartElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 300);
        }
        sessionStorage.removeItem('scrollToChart');
    }

    document.getElementById('reset-filter')?.addEventListener('click', function () {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        document.getElementById('filter-form').submit();
    });

    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            Chart.instances.forEach(chart => chart.resize());
        }, 250);
    });

    renderHorizontalBarChart('usuarioOsChart',
    {!! json_encode($usuarioOsLabels ?? []) !!},
    {!! json_encode($usuarioOsData ?? []) !!},
    colors.primary, 'OS criadas'
    );

 

    renderHorizontalBarChart('topVeiculosChart',
        {!! json_encode($topVeiculosLabels ?? []) !!},
        {!! json_encode($topVeiculosData ?? []) !!},
        colors.warning, 'Utilizações'
    );


    function renderDiaSemanaChart(id, labels, data) {
        const ctx = document.getElementById(id)?.getContext('2d');
        if (!ctx) return;
        
        const backgroundColors = [
            'rgba(231, 74, 59, 0.8)',   // Domingo - Vermelho
            'rgba(54, 185, 204, 0.8)',  // Segunda - Azul claro
            'rgba(28, 200, 138, 0.8)',  // Terça - Verde
            'rgba(246, 194, 62, 0.8)',  // Quarta - Amarelo
            'rgba(78, 115, 223, 0.8)',  // Quinta - Azul escuro
            'rgba(133, 135, 150, 0.8)', // Sexta - Cinza
            'rgba(231, 74, 59, 0.8)'    // Sábado - Vermelho
        ];
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Corridas',
                    data: data,
                    backgroundColor: backgroundColors.slice(0, labels.length),
                    borderRadius: 8,
                    maxBarThickness: 80
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { size: 11 } },
                        grid: { display: true, drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 12 }
                    }
                }
            }
        });
    }

    // Use assim:
    renderDiaSemanaChart('diaSemanaChart',
        {!! json_encode($diaSemanaLabels ?? []) !!},
        {!! json_encode($diaSemanaData ?? []) !!}
    );

});
</script>

@endpush
