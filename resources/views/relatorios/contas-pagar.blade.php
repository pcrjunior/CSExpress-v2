@extends('layouts.app')

@section('title', 'Relatório de Contas a Pagar')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Relatório de Contas a Pagar
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
                    <form method="GET" action="{{ route('relatorios.contas-pagar') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select name="entregador_id" class="form-select select2-entregadores" style="width: 100%;">
                                    <option value="">Todos</option>
                                    @foreach(App\Models\Entregador::all() as $entregador)
                                        <option value="{{ $entregador->id }}" {{ request('entregador_id') == $entregador->id ? 'selected' : '' }}>
                                            {{ $entregador->nome }}
                                        </option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="data_vencimento_inicio" class="form-label">Vencimento de</label>
                                <input type="date" class="form-control" name="data_vencimento_inicio" value="{{ request('data_vencimento_inicio') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="data_vencimento_fim" class="form-label">Vencimento até</label>
                                <input type="date" class="form-control" name="data_vencimento_fim" value="{{ request('data_vencimento_fim') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="data_pagamento_inicio" class="form-label">Pagamento de</label>
                                <input type="date" class="form-control" name="data_pagamento_inicio" value="{{ request('data_pagamento_inicio') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="data_pagamento_fim" class="form-label">Pagamento até</label>
                                <input type="date" class="form-control" name="data_pagamento_fim" value="{{ request('data_pagamento_fim') }}">
                            </div>
                            <div class="col-md-12 d-flex justify-content-end gap-2 flex-wrap mt-3">
                                <button type="submit" class="btn btn-custom-blue btn-sm">
                                    <i class="fas fa-search me-1"></i> Pesquisar
                                </button>
                                <a href="{{ route('relatorios.contas-pagar') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Limpar
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-file-export me-2"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('relatorios.contas-pagar.exportar', request()->all()) }}">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center"
                                                href="{{ route('relatorios.contas-pagar.excel', request()->all()) }}">
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
                            <th>Colaborador</th>
                            <th>Tipo</th>
                            <th>Data Vencimento</th>
                            <th>Data Pagamento</th>
                            <th class="text-end">Valor (R$)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contas as $conta)
                            <tr>
                                <td>{{ $conta->ordemServico->numero_os ?? '-' }}</td>
                                <td>{{ $conta->entregador->nome ?? '-' }} </td>
                                <td>{{ $conta->entregador->perfil ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') }}</td>
                                <td>{{ $conta->data_pagamento ? \Carbon\Carbon::parse($conta->data_pagamento)->format('d/m/Y') : '-' }}</td>
                                @php
                                    $valor = 0;
                                    $perfil = strtolower($conta->entregador->perfil ?? '');
                                    if ($perfil === 'motorista') {
                                        $valor = $conta->ordemServico->valor_repassado_motorista ?? 0;
                                    } elseif ($perfil === 'ajudante') {
                                        $valor = $conta->ordemServicoAjudantes
                                                    ->where('ajudante_id', $conta->entregador_id)
                                                    ->first()
                                                    ->valor_repassado_ajudante ?? 0;
                                    }
                                @endphp
                                <td class="text-end">R$ {{ number_format($valor, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $conta->status_pagamento === 'pago' ? 'success' : 'warning' }}">
                                        {{ ucfirst($conta->status_pagamento) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <div>Nenhuma conta encontrada para os filtros selecionados.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
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
<script>
     $(document).ready(function() {
        // Ativa o select2 para o campo entregador
        $('.select2-entregadores').select2({
            theme: 'bootstrap-5',
            placeholder: "Selecione um colaborador",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function () {
                    return "Nenhum colaborador encontrado";
                }
            }
        });

        // Submete automaticamente o formulário ao mudar o valor do select
        $('.select2-entregadores').on('change', function () {
            $('#formFiltroContasPagar').submit();
        });
       
        
    });

</script>
@endpush
