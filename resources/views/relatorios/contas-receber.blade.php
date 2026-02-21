@extends('layouts.app')

@section('title', 'Relatório de Contas a Receber')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Relatório de Contas a Receber
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
                    <form method="GET" action="{{ route('relatorios.contas-receber') }}">
                        <div class="row g-3 align-items-end">
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
                                <label class="form-label">Recebimento De</label>
                                <input type="date" name="recebimento_de" class="form-control" value="{{ request('recebimento_de') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Recebimento Até</label>
                                <input type="date" name="recebimento_ate" class="form-control" value="{{ request('recebimento_ate') }}">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-custom-blue btn-sm">
                                    <i class="fas fa-search me-1"></i> Pesquisar
                                </button>

                                <a href="{{ route('relatorios.contas-receber') }}" class="btn btn-secondary btn-sm">
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
                                            href="{{ route('relatorios.contas-receber.exportar', request()->all()) }}">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                            href="{{ route('relatorios.contas-receber.excel', request()->all()) }}">
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
                            <th>Nº OS</th>
                            <th>Cliente</th>
                            <th>Valor</th>
                            <th>Data Recebimento</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contas as $conta)
                            <tr>
                                <td>{{ $conta->ordemServico->numero_os ?? '-' }}</td>
                                @php
                                    $cliente = optional($conta->ordemServico?->cliente_contratante);
                                @endphp
                                <td>{{ $cliente->nome ?? '-' }}{{ $cliente->apelido ? ' (' . $cliente->apelido . ')' : '' }}</td>

                                <td>R$ {{ number_format($conta->valor_total, 2, ',', '.') }}</td>
                                <td>{{ $conta->data_recebimento ? \Carbon\Carbon::parse($conta->data_recebimento)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $conta->status_pagamento === 'pago' ? 'success' : 'warning' }}">
                                        {{ ucfirst($conta->status_pagamento) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <div>Nenhuma conta encontrada para os filtros selecionados.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($contas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <span class="text-muted">Exibindo {{ $contas->firstItem() }} a {{ $contas->lastItem() }} de {{ $contas->total() }} resultados</span>
                    {{ $contas->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
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
        
        $('.select2-clientes').on('change', function () {
             $(this).closest('form').submit();
        });
    </script>
@endpush
