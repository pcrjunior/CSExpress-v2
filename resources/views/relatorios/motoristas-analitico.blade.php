@extends('layouts.app')

@section('title', 'Relatório Analítico de Motoristas')

@section('content')
<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Relatório Analítico de Motoristas
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm small-table">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">Filtrar Dados</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('relatorios.motoristas-analitico') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="motorista_id" class="form-label">Motorista</label>
                                <select class="form-select select2-motoristas" name="motorista_id" style="width: 100%;">
                                    <option value="">Todos</option>
                                    @foreach($motoristas as $motorista)
                                        <option value="{{ $motorista->id }}" {{ request('motorista_id') == $motorista->id ? 'selected' : '' }}>
                                            {{ $motorista->nome }} ({{ $motorista->apelido }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Data Início</label>
                                <input type="date" class="form-control" name="data_inicio" value="{{ request('data_inicio') }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Data Fim</label>
                                <input type="date" class="form-control" name="data_fim" value="{{ request('data_fim') }}">
                            </div>

                            <div class="col-md-4 d-flex align-items-end justify-content-end gap-2 flex-wrap">
                                <button type="submit" class="btn btn-custom-blue btn-sm">
                                    <i class="fas fa-search me-1"></i> Pesquisar
                                </button>

                                <a href="{{ route('relatorios.motoristas-analitico') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-eraser me-1"></i> Limpar
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="fas fa-file-export me-2"></i> Exportar
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('relatorios.motoristas-analitico.exportar', request()->all()) }}">
                                                <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('relatorios.motoristas-analitico.excel', request()->all()) }}">
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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive small-table">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-header-blue">
                        <tr>
                            <th>Número OS</th>
                            <th>Data do Serviço</th>
                            <th>Nome Motorista</th>
                            <th class="text-end">Valor Motorista</th>
                            <th class="text-end">Valor Ajudante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dados as $item)
                            <tr>
                                <td>{{ $item['numero_os'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['data_servico'])->format('d/m/y') }}</td>
                                <td>{{ $item['nome_motorista'] }}</td>
                                <td class="text-end">R$ {{ number_format($item['valor_motorista'], 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($item['valor_ajudante'], 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Nenhum registro encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($dados->count())
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end"></th>
                                <th class="text-end">R$ {{ number_format($totalMotorista, 2, ',', '.') }}</th>
                                <th class="text-end">R$ {{ number_format($totalAjudante, 2, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL GERAL</th>
                                <th class="text-end">R$ {{ number_format($totalMotorista, 2, ',', '.') }}</th>
                                <th class="text-end">R$ {{ number_format($totalAjudante, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            <div class="d-flex justify-content-end mt-2">
                {{ $dados->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/select2/4.0.13/dist/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.select2-motoristas').select2({
            theme: 'bootstrap-5',
            placeholder: "Selecione um motorista",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
