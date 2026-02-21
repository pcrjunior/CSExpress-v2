@component('financeiro.components.section-card', ['title' => 'Lista de Contas a Receber', 'icon' => 'list'])
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
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
                        <td>{{ $conta->id }}</td>
                        <td>
                            <a href="{{ route('ordemservicos.show', $conta->ordem_servico_id) }}" class="badge bg-info text-decoration-none">
                                OS #{{ $conta->ordem_servico_id }}
                            </a>
                        </td>
                        <td>
                            {{ optional($conta->ordemServico->clienteOrigem)->nome
                                ?? optional($conta->ordemServico->clienteDestino)->nome
                                ?? '-' }}
                        </td>
                        <td>R$ {{ number_format($conta->valor_total, 2, ',', '.') }}</td>
                            <td>{{ $conta->data_vencimento->format('d/m/Y') }}</td>
                        <td>
                            @include('financeiro.components.status-badge', [
                                'status' => $conta->status_pagamento
                            ])
                        </td>
                        <td class="text-center">
                            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">

                                {{-- Botão Enviar Nota por E-mail --}}
                                <form action="{{ route('financeiro.enviar-nota-servico', $conta->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Deseja enviar a Nota de Serviço por e-mail?')"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Enviar Nota de Serviço">
                                        <i class="fas fa-paper-plane me-1"></i> Enviar NS
                                    </button>
                                </form>

                                {{-- Botão Receber --}}
                                @if($conta->status_pagamento == 'pendente')
                                    <form action="{{ route('financeiro.registrar-recebimento', $conta->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Confirmar recebimento?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> Receber
                                        </button>
                                    </form>
                                @else
                                    <small class="text-muted text-nowrap">RCB em {{ $conta->data_recebimento->format('d/m/Y') }}</small>
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

    <div class="d-flex justify-content-center mt-4">
        {{ $contasReceber->appends(request()->query())->links() }}
    </div>
@endcomponent


