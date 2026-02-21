@extends('layouts.app')

@section('title', 'Contas a Pagar')

@section('content')
<div class="container-fluid py-4">
    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Contas a Pagar</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('financeiro.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-header-blue text-white">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Pesquisar Contas
            </h6>
        </div>
        <div class="card-body">
            <form id="formFiltroContasPagar" method="GET" action="{{ route('financeiro.filtrar-contas-pagar') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Colaborador</label>
                        <select name="entregador_id" class="form-select select2-entregadores" style="width: 100%;">
                            <option value="">Todos</option>
                            @foreach(App\Models\Entregador::all() as $entregador)
                                <option value="{{ $entregador->id }}" {{ request('entregador_id') == $entregador->id ? 'selected' : '' }}>
                                    {{ $entregador->nome }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Início</label>
                        <input type="date" name="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Fim</label>
                        <input type="date" name="data_fim" class="form-control" value="{{ request('data_fim') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                            <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                        </select>
                    </div>

                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> Pesquisar
                    </button>
                    <a href="{{ route('financeiro.contas-pagar') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-eraser me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Lista de Contas -->
    <div class="card shadow-sm">

        <div class="card-body p-0">
            <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-header-blue">
                        <tr>
                            <th>OS</th>
                            <th>Colaborador</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contasPagar as $conta)
                            <tr>
                                <td>
                                    <a href="#"
                                        class="badge bg-info text-decoration-none btn-visualizar-os"
                                        data-id="{{ $conta->ordem_servico_id }}">
                                        OS #{{ $conta->ordem_servico_id }}
                                    </a>
                                </td>

                                <td>{{ $conta->entregador->nome }}</td>

                                <td>
                                    <span class="badge {{ $conta->tipo == 'motorista' ? 'bg-primary' : 'bg-info' }}">
                                        {{ ucfirst($conta->tipo) }}
                                    </span>
                                </td>


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

                                <td>R$ {{ number_format($valor, 2, ',', '.') }}</td>



                                <td>{{ $conta->data_vencimento->format('d/m/Y') }}</td>

                                <td>
                                    @include('financeiro.components.status-badge', ['status' => $conta->status_pagamento])
                                </td>

                                <td class="text-center">
                                    @if($conta->status_pagamento == 'pendente')
                                        @if($conta->tipo === 'motorista')
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="confirmarPagamentoComAjudantes({{ $conta->id }})">
                                                <i class="fas fa-check me-1"></i> Pagar
                                            </button>
                                        @else
                                            <form action="{{ route('financeiro.realizar-pagamento', $conta->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirmar pagamento?')">
                                                    <i class="fas fa-check me-1"></i> Pagar
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="badge bg-success">
                                            Pago em {{ $conta->data_pagamento->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Nenhum valor liberado para pagamento</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2">
                <small class="text-muted">
                    Exibindo {{ $contasPagar->firstItem() }} a {{ $contasPagar->lastItem() }} de {{ $contasPagar->total() }} resultados
                </small>
                {{ $contasPagar->withQueryString()->links('vendor.pagination.bootstrap-5') }}
            </div>
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

    function confirmarPagamentoComAjudantes(contaId) {
        if (!contaId) return;

        const confirmacao = confirm("Deseja pagar também os ajudantes desta OS?");
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/financeiro/contas-pagar/${contaId}/pagar`;

        // CSRF
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';

        // Método PATCH
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PATCH';

        // Pagar ajudantes ou não
        const pagarAjudantes = document.createElement('input');
        pagarAjudantes.type = 'hidden';
        pagarAjudantes.name = 'pagar_ajudantes';
        pagarAjudantes.value = confirmacao ? 'sim' : 'nao';

        // Adiciona os campos ao form
        form.appendChild(token);
        form.appendChild(method);
        form.appendChild(pagarAjudantes);

        // Adiciona o form ao body e envia
        document.body.appendChild(form);
        form.submit();
    }

    function abrirModalPagamentoMotorista(contaId, osId) {
        const form = document.getElementById('formPagamentoMotorista');
        form.action = `/financeiro/contas-pagar/${contaId}/pagar`;

        const modal = new bootstrap.Modal(document.getElementById('modalPagamentoMotorista'));
        modal.show();
    }

    function setPagarAjudantes(valor) {
        document.getElementById('inputPagarAjudantes').value = valor;
    }

    $('.select2-entregadores').select2({
        theme: 'bootstrap-5', // importante!
        placeholder: "Selecione um colaborador",
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () {
                return "Nenhum colaborador encontrado";
            }
        }
    });
</script>
@endpush
