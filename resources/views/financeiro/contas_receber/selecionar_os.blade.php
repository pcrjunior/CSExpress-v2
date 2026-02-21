@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice me-2"></i> Selecionar OS para Recebimento</h1>
        
        <a href="{{ route('financeiro.contas-receber') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-filter me-2"></i> Filtros
        </div>
        <div class="card-body">
            <form action="{{ route('financeiro.selecionar-os-recebimento') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-4">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ request('data_fim') }}">
                </div>
                <div class="col-md-4">
                    <label for="cliente_id" class="form-label">Cliente</label>
                    <select class="form-select" id="cliente_id" name="cliente_id">
                        <option value="">Todos</option>
                        @foreach(App\Models\Cliente::all() as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('financeiro.selecionar-os-recebimento') }}" class="btn btn-secondary">
                        <i class="fas fa-eraser me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-list me-2"></i> Ordens de Serviço Concluídas Disponíveis
        </div>
        <div class="card-body">
            <form action="{{ route('financeiro.gerar-recebimentos') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selecionar-todos">
                                        <label class="form-check-label" for="selecionar-todos">Sel.</label>
                                    </div>
                                </th>
                                <th>OS</th>
                                <th>Data</th>
                                <th>Cliente Origem</th>
                                <th>Cliente Destino</th>
                                <th>Motorista</th>
                                <th>Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordensServico as $os)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input os-checkbox" type="checkbox" name="ordens_servico[]" value="{{ $os->id }}" id="os-{{ $os->id }}">
                                            <label class="form-check-label" for="os-{{ $os->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('ordemservicos.show', $os->id) }}" class="badge bg-info text-decoration-none" target="_blank">
                                            {{ $os->numero_os }}
                                        </a>
                                    </td>
                                    <td>{{ $os->data_servico->format('d/m/Y') }}</td>
                                    <td>{{ $os->clienteOrigem->nome ?? 'N/A' }}</td>
                                    <td>{{ $os->clienteDestino->nome ?? 'N/A' }}</td>
                                    <td>{{ $os->motorista->nome ?? 'N/A' }}</td>
                                    <td>R$ {{ number_format($os->valor_total, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Nenhuma ordem de serviço disponível para recebimento</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $ordensServico->appends(request()->query())->links() }}
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-success" id="btn-gerar-recebimentos" disabled>
                        <i class="fas fa-file-invoice-dollar me-1"></i> Gerar Recebimentos para OS Selecionadas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script para selecionar/deselecionar todos os checkboxes
    document.getElementById('selecionar-todos').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.os-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        verificarBotao();
    });

    // Script para verificar se pelo menos um checkbox está selecionado
    document.querySelectorAll('.os-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', verificarBotao);
    });

    function verificarBotao() {
        const checkboxes = document.querySelectorAll('.os-checkbox:checked');
        const botao = document.getElementById('btn-gerar-recebimentos');
        botao.disabled = checkboxes.length === 0;
    }
</script>
@endpush
@endsection