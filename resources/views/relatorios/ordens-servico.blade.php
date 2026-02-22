@extends('layouts.app')

@section('title', 'Relatório de Ordens de Serviço')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Relatório de Ordens de Serviço
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
                    <form method="GET" action="{{ route('relatorios.ordens-servico') }}">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select" name="cliente_id" id="cliente_id">
                                    <option value="">Todos</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nome }} - {{ $cliente->apelido }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="motorista_id" class="form-label">Motorista</label>
                                <select class="form-select" name="motorista_id">
                                    <option value="">Todos</option>
                                    @foreach($motoristas as $motorista)
                                        <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>
                                            {{ $motorista->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                                <div class="form-check">
                                    <input
                                        class="form-check-input form-check-input-lg"
                                        type="checkbox"
                                        name="somente_contratante"
                                        id="somente_contratante"
                                        value="1"
                                        style="width: 2.0em; height: 2.0em; cursor: pointer;"
                                        {{ request('somente_contratante') ? 'checked' : '' }}>
                                    <label class="form-check-label fs-6 fw-semibold ms-2" for="somente_contratante" style="cursor: pointer; line-height: 2.0em;">
                                        Somente quando for contratante
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" name="data_inicio" value="{{ request('data_inicio') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" name="data_fim" value="{{ request('data_fim') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="">Todos</option>
                                    <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="em_andamento" {{ request('status') == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                    <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                    <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex justify-content-end gap-2 align-self-end">
                                <button type="submit" class="btn btn-custom-blue btn-sm">
                                    <i class="fas fa-search me-1"></i> Pesquisar
                                </button>
                                <a href="{{ route('relatorios.ordens-servico') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Limpar
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-file-export me-2"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('relatorios.ordens-servico.exportar', request()->all()) }}">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('relatorios.ordens-servico.excel', request()->all()) }}">
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
                            <th>OS</th>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Origem</th>
                            <th>Destino</th>
                            <th>Motorista</th>
                            <th>Observações</th>
                            <th class="text-end">Valor Total</th>
                            <th class="text-end">Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordens as $os)
                            @php
                                $statusColors = [
                                    'pendente' => 'warning',
                                    'em_andamento' => 'info',
                                    'concluido' => 'success',
                                    'cancelado' => 'danger',
                                ];
                                $color = $statusColors[$os->status] ?? 'secondary';
                            @endphp
                            <tr>
                                <td>
                                    <a href="#"
                                        class="badge bg-info text-decoration-none btn-visualizar-os"
                                        data-id="{{ $os->id }}">
                                        OS #{{ $os->numero_os }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($os->data_servico)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst(str_replace('_', ' ', $os->status ?? 'Indefinido')) }}
                                    </span>
                                </td>
                                <td>{{ optional($os->clienteOrigem)->nome ?? '-' }} -
                                    {{ optional($os->clienteOrigem)->apelido ?? '-' }} </td>
                                <td>{{ optional($os->clienteDestino)->nome ?? '-' }} -
                                    {{ optional($os->clienteDestino)->apelido ?? '-' }}
                                </td>

                                <td>{{ optional($os->motorista)->nome ?? '-' }}</td>

                                <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $os->observacoes ?? '-' }}
                                </td>

                                <td class="text-end">R$ {{ number_format($os->valor_total, 2, ',', '.') }}</td>
                                <td class="text-end fw-bold text-{{ $os->valor_resultado >= 0 ? 'success' : 'danger' }}">
                                    R$ {{ number_format($os->valor_repasse_resultado, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <div>Nenhuma ordem de serviço encontrada.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="7" class="text-end pe-3">
                                TOTAL GERAL:
                            </td>
                            <td class="text-end">
                                R$ {{ number_format($somaTotal, 2, ',', '.') }}
                            </td>
                            <td class="text-end {{ $somaResultado >= 0 ? 'text-success' : 'text-danger' }}">
                                R$ {{ number_format($somaResultado, 2, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Paginação -->
            @if(isset($ordens) && method_exists($ordens, 'links'))
                <div class="d-flex justify-content-between align-items-center mt-3 px-3">
                    <span class="text-muted">Resultados: {{ $ordens->total() }} registros encontrados</span>
                    {{ $ordens->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

</div>


<!-- Modal de Visualização da OS -->
<div class="modal fade" id="modalVisualizarOS" tabindex="-1" aria-labelledby="modalVisualizarOSLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalVisualizarOSLabel">Detalhes da Ordem de Serviço</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="osDetalhesBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando detalhes da OS...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-visualizar-os').on('click', function (e) {
        e.preventDefault();

        const osId = $(this).data('id');
        const url = `/ordemservicos/${osId}?visualizar_modal=1`;

        $('#osDetalhesBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Carregando detalhes da OS...</p>
            </div>
        `);

        $('#modalVisualizarOS').modal('show');

        $.get(url)
            .done(function (response) {
                $('#osDetalhesBody').html(response);
            })
            .fail(function () {
                $('#osDetalhesBody').html(`
                    <div class="alert alert-danger text-center">
                        Erro ao carregar os detalhes da Ordem de Serviço.
                    </div>
                `);
            });
    });
});
</script>
@endpush
