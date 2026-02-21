@extends('layouts.app')

@section('title', 'Contas a Receber')

@section('content')
<div class="container-fluid py-4">

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Contas a Receber
                    </h5>
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
            <form method="GET" action="{{ route('financeiro.filtrar-contas-receber') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="select2-clientes form-control">
                            <option value="">Todos</option>
                            @foreach(App\Models\Cliente::all() as $cliente)
                                <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }} ({{ $cliente->apelido }})
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
                    <a href="{{ route('financeiro.contas-receber') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-eraser me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Lista de Contas -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @include('financeiro.contas_receber._lista')
        </div>
    </div>

</div>
@endsection


@push('scripts')
<!-- Bootstrap e jQuery -->
<script src="{{ asset('js/jquery/jquery-3.6.0.min.js') }}"></script>

<link href="{{ asset('css/select2/4.0.13/dist/css/select2.min.css') }}" rel="stylesheet">
<script src="{{ asset('js/select2/4.0.13/dist/js/select2.min.js') }}"></script>



<script>

    $(document).ready(function() {
        $('.select2-clientes').select2({
            theme: 'bootstrap-5',
            placeholder: "Selecione um cliente",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Nenhum cliente encontrado";
                }
            }
        });
    });

</script>
@endpush