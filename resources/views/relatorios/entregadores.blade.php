@extends('layouts.app')

@section('title', 'Relatório de Entregadores')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Relatório de Entregadores
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
                <form method="GET" action="{{ route('relatorios.entregadores') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Entregador</label>
                            <select name="entregador_id" class="form-select">
                                <option value="">Todos</option>
                                @foreach($entregadores as $entregador)
                                    <option value="{{ $entregador->id }}" {{ request('entregador_id') == $entregador->id ? 'selected' : '' }}>
                                        {{ $entregador->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data Início</label>
                            <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data Fim</label>
                            <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                        </div>
                        <!-- <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                            <button type="submit" class="btn btn-custom-blue btn-sm">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="{{ route('relatorios.entregadores') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eraser me-1"></i> Limpar
                            </a>
                            <a href="{{ route('relatorios.entregadores.exportar', request()->query()) }}" class="btn btn-danger ms-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('relatorios.entregadores.exportar.excel', request()->query()) }}" class="btn btn-success ms-2">
                                <i class="fas fa-file-excel"></i> Excel
                            </a>
                            <a href="{{ route('relatorios.entregadores.exportar') }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a> 
                        </div> -->
                    
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
                                        href="{{ route('relatorios.entregadores.exportar', request()->all()) }}">
                                            <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center"
                                        href="{{ route('relatorios.entregadores.exportar.excel', request()->all()) }}">
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
                            <th>Nome</th>
                            <th class="text-center">Total de Ordens</th>
                            <th class="text-end">Total Pendente (R$)</th> <!-- NOVA COLUNA -->
                            <th class="text-end">Total Recebido (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dados as $linha)
                            <tr>
                                <td>{{ $linha->nome }}</td>
                                <td class="text-center">{{ $linha->total_ordens }}</td>
                                <td class="text-end text-danger">
                                    R$ {{ number_format($linha->total_pendente ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="text-end">
                                    R$ {{ number_format($linha->total_pago, 2, ',', '.') }}
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <div>Nenhum entregador encontrado.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($dados->count())
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <span class="text-muted">Exibindo {{ $dados->firstItem() }} a {{ $dados->lastItem() }} de {{ $dados->total() }} resultados</span>
                    {{ $dados->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
