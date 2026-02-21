@component('financeiro.components.section-card', ['title' => 'Lista de Contas a Receber', 'icon' => 'list'])
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-green">
                <tr>
                    <th>OS</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contasReceber as $conta)
                    <tr>
                        <td>
                            <a href="#" 
                                class="badge bg-info text-decoration-none btn-visualizar-os" 
                                data-id="{{ $conta->ordem_servico_id }}">
                                OS #{{ $conta->ordem_servico_id }}
                            </a>
                        </td>
                        <td>
                            @php
                                $contratanteTipo = $conta->ordemServico->contratante_tipo ?? null;

                                $cliente = $contratanteTipo === 'origem'
                                    ? optional($conta->ordemServico->clienteOrigem)
                                    : ($contratanteTipo === 'destino'
                                        ? optional($conta->ordemServico->clienteDestino)
                                        : null);
                            @endphp

                            {{ $cliente ? $cliente->nome . ' (' . $cliente->apelido . ')' : '-' }}
                        </td>
                        <td>R$ {{ number_format($conta->valor_total, 2, ',', '.') }}</td>
                        <td>{{ $conta->data_vencimento->format('d/m/Y') }}</td>
                        <td>
                            @include('financeiro.components.status-badge', [
                                'status' => $conta->status_pagamento
                            ])
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-wrap justify-content-center align-items-center gap-2">
                                {{-- Botão Visualizar Nota de Serviço --}}
                                <a href="{{ route('financeiro.visualizar-nota-servico-detalhada', $conta->id) }}"
                                    class="btn btn-sm btn-warning"
                                    title="Visualizar Nota de Serviço">
                                    <i class="fas fa-file-invoice me-1"></i> NS
                                </a>

                                {{-- Botão Receber ou Ícone de Recebido --}}
                                @if($conta->status_pagamento == 'pendente')
                                    <form action="{{ route('financeiro.registrar-recebimento', $conta->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Confirmar recebimento?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm px-2 py-1 d-flex align-items-center justify-content-center" title="Marcar como Recebido">
                                            <i class="fas fa-check me-1"></i> Receber
                                        </button>
                                    </form>
                                @else
                                    <span class="text-success" title="Recebido em {{ $conta->data_recebimento->format('d/m/Y') }}">
                                        <i class="fas fa-circle-check fa-lg"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Nenhuma conta a receber encontrada</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <span class="text-muted">
                Exibindo {{ $contasReceber->firstItem() }} a {{ $contasReceber->lastItem() }} de {{ $contasReceber->total() }} resultados
            </span>
        </div>
        <div>
            {{ $contasReceber->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    <!-- Modal de Visualização da Nota de Serviço -->
    <div class="modal fade" id="modalVisualizarNota" tabindex="-1" aria-labelledby="modalVisualizarNotaLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="modalVisualizarNotaLabel">Nota de Serviço</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalhesNota">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando nota de serviço...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <a href="#" id="btnImprimirNota" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </a>
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
@endcomponent

@push('scripts')
<script>
    $(document).ready(function() {
        // Configurar modal de visualização da nota de serviço
        $('.btn-visualizar-nota').click(function() {
            const id = $(this).data('id');
            const urlDetalhes = `/financeiro/contas-receber/visualizar-nota-servico/${id}`;
            const urlImprimir = `/financeiro/contas-receber/imprimir-nota-servico/${id}`;

            // Limpar conteúdo anterior e mostrar loader
            $('#detalhesNota').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando nota de serviço...</p>
                </div>
            `);

            // Configurar botão de impressão
            $('#btnImprimirNota').attr('href', urlImprimir);

            // Abrir modal
            $('#modalVisualizarNota').modal('show');

            // Carregar detalhes via AJAX
            $.ajax({
                url: urlDetalhes,
                type: 'GET',
                success: function(response) {
                    $('#detalhesNota').html(response);
                },
                error: function() {
                    $('#detalhesNota').html(`
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h5 class="text-danger">Erro ao carregar nota</h5>
                            <p>Não foi possível carregar a nota de serviço. Por favor, tente novamente.</p>
                        </div>
                    `);
                }
            });
        });
    });
</script>
@endpush

@push('scripts')
<script>
    
    $(document).ready(function() {
        $('.btn-visualizar-os').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = `/ordemservicos/${id}`;

            $('#modalVisualizarOS').modal('show');
            $('#osDetalhesBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Carregando detalhes da OS...</p>
                </div>
            `);

            $.get(url, function(data) {
                $('#osDetalhesBody').html(data);
            }).fail(function() {
                $('#osDetalhesBody').html(`
                    <div class="text-danger text-center py-5">
                        <i class="fas fa-exclamation-circle fa-2x"></i>
                        <p>Erro ao carregar detalhes da OS.</p>
                    </div>
                `);
            });
        });
    });

    function confirmarEnvio(form) {
        const confirmacao = confirm('Deseja enviar a Nota de Serviço por e-mail?');
        if (!confirmacao) return false;

        const button = form.querySelector('button');
        const textSpan = button.querySelector('.btn-text');
        const spinner = button.querySelector('.spinner-border');

        if (textSpan && spinner) {
            textSpan.classList.add('d-none');
            spinner.classList.remove('d-none');
        }

        button.disabled = true;

        return true;
    }

    
</script>
@endpush
