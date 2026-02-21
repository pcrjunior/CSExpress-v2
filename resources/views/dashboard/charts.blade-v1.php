@extends('layouts.app')

@section('title', 'Dashboard de Entregas - Análise de Desempenho')

@section('content')

<div class="container-fluid py-4">
    <!-- Título -->
    <div class="row mb-2">
        <div class="col-md-12 mb-2">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Dashboard Operacional</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de resumo -->
    <div class="row mb-3">
        <!-- Ordem de Serviço -->
        <div class="col-md-3 mb-2">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-subtitle text-secondary mb-2">Ordem de Serviço</h6>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-tools text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center flex-fill border-end">
                            <h2 class="fw-bold text-warning mb-0">{{ $totalOSAberta }}</h2>
                            <small class="text-muted">Em Andamento</small>
                        </div>
                        <div class="text-center flex-fill">
                            <h2 class="fw-bold text-success mb-0">{{ $totalOS }}</h2>
                            <small class="text-muted">Finalizadas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes -->
        <div class="col-md-3 mb-2">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-subtitle text-secondary mb-2">Clientes</h6>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-person-lines-fill text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center flex-fill border-end">
                            <h2 class="fw-bold text-primary mb-0">{{ $totalClientes }}</h2>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="text-center flex-fill">
                            <h2 class="fw-bold text-success mb-0">{{ $clientesComOSAberta }}</h2>
                            <small class="text-muted">Com OS Abertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colaboradores -->
        <div class="col-md-3 mb-2">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-subtitle text-secondary mb-2">Colaboradores</h6>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-people-fill text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center flex-fill border-end">
                            <h2 class="fw-bold text-primary mb-0">{{ $totalColaboradores }}</h2>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="text-center flex-fill">
                            <h2 class="fw-bold text-success mb-0">{{ $entregadoresComOSAberta }}</h2>
                            <small class="text-muted">Com OS Abertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Veículos -->
        <div class="col-md-3 mb-2">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-subtitle text-secondary mb-2">Veículos</h6>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i class="bi bi-truck text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-center flex-fill border-end">
                            <h2 class="fw-bold text-primary mb-0">{{ $totalVeiculos }}</h2>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="text-center flex-fill">
                            <h2 class="fw-bold text-success mb-0">{{ $veiculosComOSAberta }}</h2>
                            <small class="text-muted">Com OS Abertas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtro de período -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-3 shadow-sm">
                <div class="card-body">

                    <form method="GET" action="{{ route('dashboard.charts') }}" id="filter-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <div>
                                    <h5 class="card-title mb-3">
                                        <i class="bi bi-funnel-fill me-2 text-primary"></i>Filtros
                                    </h5>
                                    </div>
                                <label for="start_date" class="form-label text-secondary small">Data Inicial</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-calendar3"></i>
                                    </span>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control border-start-0 ps-0"
                                           value="{{ $startDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label text-secondary small">Data Final</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-calendar3"></i>
                                    </span>
                                    <input type="date" name="end_date" id="end_date"
                                           class="form-control border-start-0 ps-0"
                                           value="{{ $endDate }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-3">
                                    <button type="submit" id="aplicar-filter" class="btn btn-primary px-7" onclick="sessionStorage.setItem('scrollToChart', '1')">
                                        <i class="bi bi-search me-2"></i>Aplicar Filtros
                                    </button>
                                    <button type="button" id="reset-filter" class="btn btn-outline-secondary px-7">
                                        <i class="bi bi-x-circle me-2"></i>Limpar Filtro
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de entregas -->
        <div class="col-md-6 mb-4" id="scroll-to-chart">

            <div class="card border-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Quantidade de Entregas</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <canvas id="entregasPorDiaChart" style="height: 260px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de OS criadas por status -->
        <div class="col-md-6 mb-4">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">OS por Situação</h6>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <canvas id="osStatusChart" width="200" height="200" style="max-width: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Top 10 Colaboradores com Mais OS</h6>
                </div>
                <div class="card-body">
                    <canvas id="topColaboradoresChart" width="200" height="200" style="max-width: 700px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-3 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Top 10 Clientes Contratantes</h6>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="topClientesChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>



    <!-- Hidden inputs (se necessário para JS externo) -->
    <input type="hidden" id="status-labels" value='@json($entregadoresStatusLabels)'>
    <input type="hidden" id="status-data" value='@json($entregadoresStatusData)'>
</div>
@endsection

@push('scripts')

<script src="{{ asset('js/npm/chart.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 🍕 Gráfico de Pizza
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
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: false }
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
                    borderRadius: 4,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                plugins: {
                    legend: { display: label !== '' },
                    title: { display: false }
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
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.x} ${tooltipLabel}`;
                        }
                    }
                },
                title: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        font: {
                            size: 9 // 👈 AQUI você controla o tamanho da fonte dos nomes
                        }
                    }
                }
            }
        }
    });
}

    // Chamada dos gráficos
    renderPizzaChart('osStatusChart',
        {!! json_encode($osCriadasStatusLabels ?? []) !!},
        {!! json_encode($osCriadasStatusData ?? []) !!},
        ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc', '#858796']
    );

    renderBarChart('entregasPorDiaChart',
        {!! json_encode($entregasPorDiaLabels ?? []) !!},
        {!! json_encode($entregasPorDiaData ?? []) !!},
        '#4e73df', 'OS Criadas'
    );

    renderHorizontalBarChart('topColaboradoresChart',
        {!! json_encode($topColaboradoresLabels ?? []) !!},
        {!! json_encode($topColaboradoresData ?? []) !!},
        '#36b9cc', 'OS por colaborador'
    );

    renderHorizontalBarChart('topClientesChart',
        {!! json_encode($topClientesLabels ?? []) !!},
        {!! json_encode($topClientesData ?? []) !!},
        '#4e73df', 'OS contratadas'
    );

    // Verifica se precisa rolar até o gráfico
    if (sessionStorage.getItem('scrollToChart')) {
        const chartElement = document.getElementById('scroll-to-chart');
        if (chartElement) {
            chartElement.scrollIntoView({ behavior: 'smooth' });
        }
        sessionStorage.removeItem('scrollToChart');
    }

    // Reset do filtro
    document.getElementById('reset-filter')?.addEventListener('click', function () {
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        document.getElementById('filter-form').submit();
    });
});

document.getElementById('reset-filter')?.addEventListener('click', function () {
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('filter-form').submit();
});
</script>

@endpush
