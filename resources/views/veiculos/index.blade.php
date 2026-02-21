@extends('layouts.app')

@section('title', 'Gerenciamento de Veículos')

@section('content')
<div class="container-fluid py-4">

    <!-- Mensagem de Sucesso -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="alert-success">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-car me-2"></i>Veículos</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('veiculos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Veículo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm small-table">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">Pesquisar Veículos</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('veiculos.index') }}" method="GET" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="placa" class="form-label">Placa</label>
                                <input type="text" name="placa" id="placa" class="form-control" value="{{ request('placa') }}" placeholder="AAA-9999 ou AAA-9A99" autocomplete="off" required maxlength="8">
                            </div>
                            <!-- <div class="col-md-4">
                                <label for="fabricante" class="form-label">Fabricante</label>
                                <input type="text" name="fabricante" id="fabricante" class="form-control" value="{{ request('fabricante') }}" placeholder="Digite o fabricante" autocomplete="off">
                            </div> -->
                            <div class="col-md-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <input type="text" name="categoria" id="categoria" class="form-control" value="{{ request('categoria') }}" placeholder="Digite a categoria" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" name="modelo" id="modelo" class="form-control" value="{{ request('modelo') }}" placeholder="Digite o modelo" autocomplete="off">
                            </div>
                            <!-- <div class="col-md-4">
                                <label for="ano_modelo" class="form-label">Ano Modelo</label>
                                <input type="text" name="ano_modelo" id="ano_modelo" class="form-control" value="{{ request('ano_modelo') }}" placeholder="Digite o ano modelo" autocomplete="off">
                            </div> -->
                            
                            <div class="col-md-3">
                                <label for="dia_rodizio" class="form-label">Dia Rodízio</label>
                                <input type="text" name="dia_rodizio" id="dia_rodizio" class="form-control" value="{{ request('dia_rodizio') }}" placeholder="Digite o dia do rodízio" autocomplete="off">
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-custom-blue btn-sm">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="{{ route('veiculos.index') }}" class="btn btn-secondary btn-sm" role="button">
                                <i class="fas fa-eraser me-1"></i> Limpar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Tabela de Veículos -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive small-table">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-header-blue">
                        <tr class="text-uppercase">
                            <th>Placa</th>
                            <th>Fabricante</th>
                            <th>Modelo</th>
                            <th>Ano Fabricação</th>
                            <th>Ano Modelo</th>
                            <th>Categoria</th>
                            <th>Cor</th>
                            <th>Rodízio</th>
                            <th>Dia Rodízio</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($veiculos as $veiculo)
                            <tr>
                                <td>{{ $veiculo->placa }}</td>
                                <td>{{ $veiculo->fabricante }}</td>
                                <td>{{ $veiculo->modelo }}</td>
                                <td>{{ $veiculo->ano_fabricacao }}</td>
                                <td>{{ $veiculo->ano_modelo }}</td>
                                <td>{{ $veiculo->categoria }}</td>
                                <td>{{ $veiculo->cor }}</td>
                                <td>{{ $veiculo->rodizio ? 'Sim' : 'Não' }}</td>
                                <td>{{ $veiculo->dia_rodizio ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('veiculos.edit', $veiculo->id) }}" class="btn btn-action btn-action-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('veiculos.destroy', $veiculo->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-action-delete btn-excluir" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este veículo?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <div>Nenhum veículo encontrado.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    @if(isset($veiculos) && method_exists($veiculos, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Exibindo {{ $veiculos->firstItem() }} a {{ $veiculos->lastItem() }} de {{ $veiculos->total() }} resultados
            </div>
            <div>
                {{ $veiculos->withQueryString()->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(() => {
            const alert = document.getElementById("alert-success");
            if(alert) alert.style.display = "none";
        }, 3000);

        const placaInput = document.getElementById('placa');

        placaInput.addEventListener('input', function () {
            let valor = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

            if (valor.length > 3) {
                valor = valor.slice(0, 3) + '-' + valor.slice(3);
            }

            this.value = valor.slice(0, 8);
        });


    });




</script>
@endpush
