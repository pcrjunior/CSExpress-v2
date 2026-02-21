@extends('layouts.app')

@section('title', 'Relatório de Clientes Atendidos')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Relatório de Clientes Atendidos
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
                    <form method="GET" action="{{ route('relatorios.clientes-atendidos') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select select2-clientes" name="cliente_id" style="width: 100%;">
                                    <option value="">Todos</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }} ({{ $cliente->apelido }})
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

                            <div class="col-md-3 d-flex align-items-end justify-content-end gap-2 flex-wrap">
                                <button type="submit" class="btn btn-custom-blue btn-sm">
                                    <i class="fas fa-search me-1"></i> Pesquisar
                                </button>

                                <a href="{{ route('relatorios.clientes-atendidos') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Limpar
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-label="Exportar Relatório">
                                        <i class="fas fa-file-export me-2"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('relatorios.clientes-atendidos.exportar', request()->all()) }}">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('relatorios.clientes-atendidos.excel', request()->all()) }}">
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
                            <th>Cliente</th>
                            <th>Total de Ordens</th>
                            <th class="text-end">Valor Total (R$)</th>
                            <th class="text-center">Última OS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dados as $cliente)
                            <tr>
                                <td>{{ $cliente['nome'] }}</td>
                                <td>{{ $cliente['total_os'] }}</td>
                                <td class="text-end">R$ {{ number_format($cliente['valor_total'], 2, ',', '.') }}</td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($cliente['ultima_os'])->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <div>Nenhum cliente atendido no período.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($dados instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <span class="text-muted">Exibindo {{ $dados->firstItem() }} a {{ $dados->lastItem() }} de {{ $dados->total() }} resultados</span>
                    {{ $dados->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/select2/4.0.13/dist/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.select2-clientes').select2({
                theme: 'bootstrap-5',
                placeholder: "Selecione um cliente",
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function () {
                        return "Nenhum cliente encontrado";
                    }
                }
            });
        });
    </script>
@endpush
