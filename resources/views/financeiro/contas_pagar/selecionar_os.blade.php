<!-- resources/views/financeiro/contas_pagar/selecionar_os.blade.php -->
@extends('financeiro.layouts.financeiro')

@section('content')
    @component('financeiro.layouts.financeiro')
        @slot('pageTitle', 'Selecionar OS para Pagamento')
        @slot('icon', 'file-invoice-dollar')

        @slot('actions')
            <a href="{{ route('financeiro.contas-pagar') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        @endslot

        @component('financeiro.components.section-card', ['title' => 'Filtros', 'icon' => 'filter'])
            @component('financeiro.components.filter-form', [
                'route' => route('financeiro.selecionar-os-pagamento'),
                'clearRoute' => route('financeiro.selecionar-os-pagamento')
            ])
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
            @endcomponent
        @endcomponent

        @component('financeiro.components.section-card', ['title' => 'Ordens de Serviço Disponíveis', 'icon' => 'list'])
            <form action="{{ route('financeiro.gerar-pagamentos') }}" method="POST">
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
                                <th>Cliente</th>
                                <th>Motorista</th>
                                <th>Ajudantes</th>
                                <th>Valor Motorista</th>
                                <th>Valor Ajudantes</th>
                                <th>Total</th>
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
                                        <a href="{{ route('ordemservicos.show', $os->id) }}" class="badge bg-info text-decoration-none">
                                            OS #{{ $os->id }}
                                        </a>
                                    </td>
                                    <td>{{ $os->data_servico->format('d/m/Y') }}</td>
                                    <td>{{ $os->cliente->nome }}</td>
                                    <td>{{ $os->motorista->nome ?? 'N/A' }}</td>
                                    <td>{{ $os->ajudantes->count() }}</td>
                                    <td>R$ {{ number_format($os->valor_motorista, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($os->ajudantes->sum('pivot.valor'), 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($os->getTotalPagarAttribute(), 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nenhuma ordem de serviço disponível para pagamento</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $ordensServico->appends(request()->query())->links() }}
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success" id="btn-gerar-pagamentos" disabled>
                        <i class="fas fa-money-bill-wave me-1"></i> Gerar Pagamentos para OS Selecionadas
                    </button>
                </div>
            </form>
        @endcomponent

        @slot('scripts')
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
                    const botao = document.getElementById('btn-gerar-pagamentos');
                    botao.disabled = checkboxes.length === 0;
                }
            </script>
        @endslot
    @endcomponent
@endsection
