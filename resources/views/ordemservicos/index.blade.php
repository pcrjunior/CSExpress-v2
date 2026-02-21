@extends('layouts.app')

@section('title', 'Ordens de Serviço')

@section('content')


<div class="container-fluid py-4">

    <!-- Card de título -->
    <div class="row mb-1">
        <div class="col-12 mb-2">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Ordem de Serviço</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('ordemservicos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Nova Ordem
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="col-12 mb-2">
        <div class="card shadow-sm">
            <div class="card-header bg-header-blue">
                <h5 class="mb-0">Filtros de Pesquisa</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('ordemservicos') }}" method="GET" id="formFiltros">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="filtro_cliente" class="form-label">Cliente</label>
                            <select class="form-select" id="filtro_cliente" name="cliente_id">
                                <option value="">Todos</option>
                                @foreach($clientes ?? [] as $cliente)
                                    <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_apelido" class="form-label">Apelido</label>
                            <select class="form-select" id="filtro_apelido" name="apelido">
                                <option value="">Todos</option>
                                @foreach(($clientes ?? collect())->sortBy('apelido') as $cliente)
                                    <option value="{{ $cliente->apelido }}" {{ request('apelido') == $cliente->apelido ? 'selected' : '' }}>
                                        {{ $cliente->apelido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="filtro_data_inicio" class="form-label">Data Inicial</label>
                            <input type="date" class="form-control" id="filtro_data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_data_fim" class="form-label">Data Final</label>
                            <input type="date" class="form-control" id="filtro_data_fim" name="data_fim" value="{{ request('data_fim') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_motorista" class="form-label">Motorista</label>
                            <select class="form-select" id="filtro_motorista" name="motorista_id">
                                <option value="">Todos</option>
                                @foreach($motoristas ?? [] as $motorista)
                                    <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>
                                        {{ $motorista->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_status" class="form-label">Status</label>
                            <select class="form-select" id="filtro_status" name="status">
                                <option value="">Todos</option>
                                <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                <option value="em_andamento" {{ request('status') == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro_numero_os" class="form-label">Número OS</label>
                            <input type="text" class="form-control" id="filtro_numero_os" name="numero_os" value="{{ request('numero_os') }}" placeholder="Ex: OS00123">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Filtrar
                                </button>
                                <button type="button" id="btnLimparFiltros" class="btn btn-outline-secondary">
                                    <i class="fas fa-eraser me-1"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabela de Resultados -->
    <div class="col-12 mb-2">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-sm">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Data</th>
                                <!-- <th>Empresa</th> -->
                                <th>Cliente Origem</th>
                                <th>Cliente Destino</th>
                                <th>Motorista</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordensServico ?? [] as $ordem)
                            <tr>
                                <td>{{ $ordem->numero_os }}</td>
                                <td>{{ \Carbon\Carbon::parse($ordem->data_servico)->format('d/m/Y') }}</td>
                                <!-- <td>{{ $ordem->empresa->nome ?? 'N/A' }}</td> -->
                                <td style="width: 320px;">
                                    {{ $ordem->clienteOrigem->nome ?? 'N/A' }}<br>
                                    <strong>{{ $ordem->clienteOrigem->apelido ?? '' }}</strong>
                                </td>
                                <td style="width: 320px;">
                                    {{ $ordem->clienteDestino->nome ?? 'N/A' }}<br>
                                    <strong>{{ $ordem->clienteDestino->apelido ?? '' }}</strong>
                                </td>



                                <td>{{ $ordem->motorista->nome ?? 'N/A' }}</td>
                                <td>R$ {{ number_format($ordem->valor_total, 2, ',', '.') }}</td>
                                <td>
                                    @if($ordem->status == 'pendente')
                                        <span class="badge bg-warning text-dark">Pendente</span>
                                    @elseif($ordem->status == 'em_andamento')
                                        <span class="badge bg-info text-dark">Em Andamento</span>
                                    @elseif($ordem->status == 'concluido')
                                        <span class="badge bg-success">Concluído</span>
                                    @elseif($ordem->status == 'cancelado')
                                        <span class="badge bg-danger">Cancelado</span>
                                    @else
                                        <span class="badge bg-secondary">Não Definido</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group">
                                        <a href="{{ route('ordemservicos.edit', $ordem->id) }}" class="btn btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-info btn-visualizar" title="Visualizar" data-id="{{ $ordem->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-excluir" title="Excluir" data-id="{{ $ordem->id }}" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Nenhuma ordem de serviço encontrada</h5>
                                        <p class="text-muted">Tente ajustar os filtros ou crie uma nova ordem de serviço.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span class="text-muted">
                            Exibindo {{ $ordensServico->firstItem() }} a {{ $ordensServico->lastItem() }} de {{ $ordensServico->total() }} resultados
                        </span>
                    </div>
                    <div>
                        {{ $ordensServico->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalExcluirLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta ordem de serviço?</p>
                <p><strong>Esta ação não poderá ser desfeita.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visualização -->
<div class="modal fade" id="modalVisualizar" tabindex="-1" aria-labelledby="modalVisualizarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalVisualizarLabel">Detalhes da Ordem de Serviço</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detalhesOS">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando detalhes...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <!-- <a href="#" id="btnEditarOS" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a> -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Limpar filtros
        $('#btnLimparFiltros').click(function() {
            $('#formFiltros').find('input, select').val('');
            $('#formFiltros').submit();
        });

        // Configurar modal de exclusão
        $('.btn-excluir').click(function() {
            const id = $(this).data('id');
            const url = "{{ route('ordemservicos.destroy', ':id') }}".replace(':id', id);
            $('#formExcluir').attr('action', url);
        });

        // Configurar modal de visualização
        $('.btn-visualizar').click(function() {
            const id = $(this).data('id');
            const urlDetalhes = "{{ route('ordemservicos.show', ':id') }}".replace(':id', id);
            const urlEditar = "{{ route('ordemservicos.edit', ':id') }}".replace(':id', id);



            // Limpar conteúdo anterior e mostrar loader
            $('#detalhesOS').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando detalhes...</p>
                </div>
            `);

            // Configurar botão de edição
            $('#btnEditarOS').attr('href', urlEditar);

            // Abrir modal
            $('#modalVisualizar').modal('show');

            // Carregar detalhes via AJAX
            $.ajax({
                url: urlDetalhes,
                type: 'GET',
                success: function(response) {
                    $('#detalhesOS').html(response);
                },
                error: function() {
                    $('#detalhesOS').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h5 class="text-danger">Erro ao carregar detalhes</h5>
                            <p>Não foi possível carregar os detalhes da ordem de serviço. Por favor, tente novamente.</p>
                        </div>
                    `);
                }
            });
        });
    });
</script>
@endpush
