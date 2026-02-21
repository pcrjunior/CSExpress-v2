<div class="container-fluid">
    <div class="row">
        <!-- Coluna esquerda -->
        <div class="col-md-6 mb-3">
            <h5 class="text-primary mb-3">
                <i class="fas fa-file-invoice me-1"></i> Informações da Nota
            </h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>Número:</strong> {{ $nota->numero_nota }}
                </li>
                <li class="list-group-item">
                    <strong>Data de Emissão:</strong> {{ \Carbon\Carbon::parse($nota->data_emissao)->format('d/m/Y') }}
                </li>
                <li class="list-group-item">
                    <strong>Valor Total:</strong> R$ {{ number_format($nota->valor_total, 2, ',', '.') }}
                </li>
                <li class="list-group-item">
                    <strong>Status:</strong>
                    @php
                        $status = $nota->status;
                        $classes = [
                            'emitida' => 'badge bg-success',
                            'pendente' => 'badge bg-warning text-dark',
                            'cancelada' => 'badge bg-danger'
                        ];
                    @endphp
                    <span class="{{ $classes[$status] ?? 'badge bg-secondary' }}">
                        {{ ucfirst($status) }}
                    </span>
                </li>
            </ul>
        </div>

        <!-- Coluna direita -->
        <div class="col-md-6 mb-3">
            <h5 class="text-primary mb-3">
                <i class="fas fa-user me-1"></i> Cliente e Ordem
            </h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>Cliente:</strong> {{ $nota->cliente->nome ?? 'N/A' }}
                </li>
                <li class="list-group-item">
                    <strong>CPF/CNPJ:</strong> {{ $nota->cliente->documento ?? '-' }}
                </li>
                <li class="list-group-item">
                    <strong>Referente à Ordem:</strong> {{ $nota->ordemServico->numero_os ?? 'N/A' }}
                </li>
                <li class="list-group-item">
                    <strong>Data do Serviço:</strong>
                    {{ \Carbon\Carbon::parse($nota->ordemServico->data_servico ?? null)->format('d/m/Y') ?? '-' }}
                </li>
            </ul>
        </div>
    </div>

    @if(!empty($nota->observacoes))
    <div class="row mt-3">
        <div class="col-12">
            <h6 class="text-muted">Observações</h6>
            <div class="alert alert-secondary">
                {{ $nota->observacoes }}
            </div>
        </div>
    </div>
    @endif
</div>
