@extends('layouts.app')

@section('title', 'Relatório de Motoristas')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>Relatório de Motoristas
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm small-table">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">Filtrar Dados</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('relatorios.motoristas') }}" autocomplete="off">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="motorista_id" class="form-label">Motorista</label>
                                <select name="motorista_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($motoristas as $motorista)
                                        <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>
                                            {{ $motorista->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" name="data_inicio" value="{{ request('data_inicio') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" name="data_fim" value="{{ request('data_fim') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status_pagamento" class="form-label">Status do Pagamento</label>
                                <select name="status_pagamento" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="pago" {{ request('status_pagamento') == 'pago' ? 'selected' : '' }}>Pagas</option>
                                    <option value="pendente" {{ request('status_pagamento') == 'pendente' ? 'selected' : '' }}>Pendentes</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex justify-content-end align-items-end gap-2">
                                <button type="submit" class="btn btn-custom-blue btn-sm">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-file-export me-2"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('relatorios.motoristas.exportar', request()->all()) }}">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('relatorios.motoristas.exportar.excel', request()->all()) }}">
                                                <i class="fas fa-file-excel me-2 text-success"></i> Excel
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive small-table">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-header-blue">
                        <tr>
                            <th>Motorista</th>
                            <th class="text-center">Total de Ordens</th>
                            <th class="text-end">Total Pago (R$)</th>
                            <th class="text-end">Total Pendente (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dados as $dado)
                            <tr>
                                <td>{{ $dado->nome ?? '-' }}</td>
                                <td class="text-center">{{ $dado->total_ordens ?? 0 }}</td>
                                <td class="text-end">R$ {{ number_format($dado->total_pago ?? 0, 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($dado->total_pendente ?? 0, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <div>Nenhum motorista encontrado.</div>
                                </td>
                            </tr>
                        @endforelse

                        @if(count($dados))
                            <tr class="fw-bold bg-light">
                                <td class="text-end">Totais:</td>
                                <td class="text-center">{{ $dados->sum('total_ordens') }}</td>
                                <td class="text-end">R$ {{ number_format($dados->sum('total_pago'), 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($dados->sum('total_pendente'), 2, ',', '.') }}</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($dados instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <span class="text-muted">Resultados: {{ $dados->total() }} registros encontrados</span>
                    {{ $dados->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
