@component('financeiro.components.section-card', ['title' => 'Lista de Contas a Pagar', 'icon' => 'list'])
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
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
                            <a href="javascript:void(0);" onclick="visualizarOrdemServico({{ $conta->ordem_servico_id }})" class="badge bg-info text-decoration-none">
                                OS #{{ $conta->ordem_servico_id }}
                            </a>
                        </td>

                        <td>{{ $conta->entregador->nome }}</td>

                        <td>
                            <span class="badge {{ $conta->tipo == 'motorista' ? 'bg-primary' : 'bg-info' }}">
                                {{ ucfirst($conta->tipo) }}
                            </span>
                        </td>

                        <td>R$ {{ number_format($conta->valor_total, 2, ',', '.') }}</td>






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
                                <span class="text-muted">Pago em {{ $conta->data_pagamento->format('d/m/Y') }}</span>
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
    <!-- Paginação -->

    <!-- <div class="d-flex justify-content-center mt-4">
        {{ $contasPagar->appends(request()->query())->links() }}
    </div> -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">
                Exibindo {{ $contasPagar->firstItem() }} a {{ $contasPagar->lastItem() }} de {{ $contasPagar->total() }} resultados
            </span>
        </div>
        <div>
            {{ $contasPagar->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endcomponent

<!-- Modal de Visualização (pré-renderizado para evitar problemas de criação dinâmica) -->
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
                <a href="#" id="btnEditarOS" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Editar
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function visualizarOrdemServico(id) {
        // URL para os detalhes da ordem de serviço
        const urlDetalhes = "/ordemservicos/" + id;
        const urlEditar = "/ordemservicos/" + id + "/edit";

        // Configurar o botão de edição
        document.getElementById('btnEditarOS').href = urlEditar;

        // Mostrar o spinner enquanto carrega
        document.getElementById('detalhesOS').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2">Carregando detalhes...</p>
            </div>
        `;

        // Abrir o modal (usando Bootstrap 5)
        const modal = new bootstrap.Modal(document.getElementById('modalVisualizar'));
        modal.show();

        // Carregar os detalhes via AJAX
        fetch(urlDetalhes)
            .then(response => response.text())
            .then(html => {
                document.getElementById('detalhesOS').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('detalhesOS').innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">Erro ao carregar detalhes</h5>
                        <p>Não foi possível carregar os detalhes da ordem de serviço. Por favor, tente novamente.</p>
                    </div>
                `;
            });
    }

    // jQuery document ready
    $(document).ready(function() {
        // Garante que o botão visualizar da index também use a mesma função
        $(document).on('click', '.btn-visualizar', function() {
            visualizarOrdemServico($(this).data('id'));
            return false;
        });
    });

    function confirmarPagamentoComAjudantes(contaId) {
        if (confirm("Deseja pagar também os ajudantes desta OS?")) {
            // Redireciona com flag para pagar ajudantes também
            window.location.href = `/financeiro/contas-pagar/${contaId}/pagar?ajudantes=sim`;
        } else {
            // Redireciona para pagar apenas o motorista
            window.location.href = `/financeiro/contas-pagar/${contaId}/pagar`;
        }
    }
</script>
@endpush
