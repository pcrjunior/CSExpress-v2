@extends('layouts.app')

@section('title', 'Gerenciamento de colaboradores')

@section('content')
<div class="container-fluid py-4">
    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Colaborador</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('entregadores.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Colaborador
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagem de sucesso -->
    @if(session('success'))
        <div id="alert-success" class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif



    <!-- Filtro -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm small-table">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">Pesquisar Colaboradores</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('entregadores.index') }}" method="GET" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" name="nome" id="nome" class="form-control" value="{{ request('nome') }}" placeholder="Digite o nome" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" name="cpf" id="cpf" class="form-control" value="{{ request('cpf') }}" placeholder="Digite o CPF" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label for="perfil" class="form-label">Perfil</label>
                                <select name="perfil" id="perfil" class="form-select">
                                    <option value="">Selecione</option>
                                    <option value="Motorista" {{ request('perfil') == 'Motorista' ? 'selected' : '' }}>Motorista</option>
                                    <option value="Ajudante" {{ request('perfil') == 'Ajudante' ? 'selected' : '' }}>Ajudante</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="categoria" class="form-label">CNH Categoria</label>
                                <input type="text" name="categoria" id="categoria" class="form-control" value="{{ request('categoria') }}" placeholder="Categoria" autocomplete="off">
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-custom-blue btn-sm">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="{{ route('entregadores.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eraser me-1"></i> Limpar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela Desktop -->
    <div class="d-none d-md-block">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive small-table">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-header-blue">
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Data Nasc.</th>
                                <th>CNH Nº</th>
                                <th>Validade CNH</th>
                                <th>Categoria</th>
                                <th>Perfil</th>
                                <th>Veículos</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entregadores as $entregador)
                                <tr>
                                    <td>{{ $entregador->nome }}</td>
                                    <td>{{ $entregador->cpf }}</td>
                                    <td>{{ optional($entregador->data_nascimento)->format('d/m/Y') }}</td>
                                    <td>{{ $entregador->cnh_numero ?? 'N/I' }}</td>
                                    <td>{{ optional($entregador->cnh_validade)->format('d/m/Y') ?? 'Não informado' }}</td>
                                    <td>{{ $entregador->cnh_categoria }}</td>
                                    <td>{{ $entregador->perfil }}</td>
                                    @php
                                        $veiculosJson = $entregador->veiculos->map(function($v) {
                                            return [
                                                'categoria' => $v->categoria,
                                                'fabricante' => $v->fabricante,
                                                'modelo' => $v->modelo,
                                                'placa' => $v->placa,
                                            ];
                                        })->toJson();
                                    @endphp

                                    <td>
                                        @if(strtolower($entregador->perfil) === 'motorista' && $entregador->veiculos->isNotEmpty())
                                            <button class="btn btn-sm btn-link ver-veiculos"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalVeiculos"
                                                data-entregador="{{ $entregador->nome }}"
                                                data-veiculos='{{ $veiculosJson }}'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>



                                    <td>
                                        <span class="badge {{ $entregador->active ? 'badge-status-ativo' : 'badge-status-desabilitado' }}">
                                            {{ $entregador->active ? 'Ativo' : 'Desabilitado' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('entregadores.edit', $entregador->id) }}"
                                               class="btn btn-action btn-action-edit"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('entregadores.toggle', $entregador->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="btn btn-action btn-action-toggle btn-toggle"
                                                        title="{{ $entregador->active ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('entregadores.destroy', $entregador->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirma exclusão?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-action btn-action-delete btn-excluir"
                                                        title="Excluir">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela Mobile -->
    <div class="d-md-none">
        @foreach($entregadores as $entregador)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-primary">{{ $entregador->nome }}</h5>
                    <p><strong>CPF:</strong> {{ $entregador->cpf }}</p>
                    <p><strong>Nasc.:</strong> {{ optional($entregador->data_nascimento)->format('d/m/Y') }}</p>
                    <p><strong>CNH:</strong> {{ $entregador->cnh_numero ?? 'N/I' }} ({{ $entregador->cnh_categoria }})</p>
                    <p><strong>Validade:</strong> {{ optional($entregador->cnh_validade)->format('d/m/Y') ?? 'Não informado' }}</p>
                    <p><strong>Perfil:</strong> {{ $entregador->perfil }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge {{ $entregador->active ? 'badge-status-ativo' : 'badge-status-desabilitado' }}">
                            {{ $entregador->active ? 'Ativo' : 'Desabilitado' }}
                        </span>
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('entregadores.edit', $entregador->id) }}" class="btn btn-xs btn-outline-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form action="{{ route('entregadores.toggle', $entregador->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-xs btn-outline-warning">
                                <i class="fas fa-power-off"></i> {{ $entregador->active ? 'Desabilitar' : 'Habilitar' }}
                            </button>
                        </form>
                        <form action="{{ route('entregadores.destroy', $entregador->id) }}" method="POST" onsubmit="return confirm('Confirma exclusão?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


 <!-- Paginação -->
@if(isset($entregadores) && method_exists($entregadores, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Exibindo {{ $entregadores->firstItem() }} a {{ $entregadores->lastItem() }} de {{ $entregadores->total() }} resultados
        </div>
        <div>
            {{ $entregadores->withQueryString()->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endif
</div>

<!-- Modal Veículos (Melhorado UX/UI) -->
<div class="modal fade" id="modalVeiculos" tabindex="-1" aria-labelledby="modalVeiculosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-sm border-0 rounded-3">

            <!-- Cabeçalho -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-primary">
                    Veículos de : <span id="nomeEntregador" class="text-dark"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo -->
            <div class="modal-body px-4 py-3">
                <ul class="list-group list-group-flush" id="listaVeiculos"></ul>
            </div>

            <!-- Rodapé -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Exibe veículos no modal
        $('.ver-veiculos').on('click', function() {
        const nome = $(this).data('entregador');
        const veiculos = $(this).data('veiculos') || [];

        $('#nomeEntregador').text(nome);
        $('#listaVeiculos').empty();

        console.log(veiculos); // ✅ Verifique no console os dados retornados

        if (veiculos.length > 0) {
            veiculos.forEach(v => {
                const categoria = v.categoria ?? '-';
                const fabricante = v.fabricante ?? '-';
                const modelo = v.modelo ?? '-';
                const placa = v.placa ?? '-';

                $('#listaVeiculos').append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div><strong>${categoria}</strong></div>
                            <div>${fabricante} - ${modelo}</div>
                        </div>
                        <div><strong>${placa}</strong></div>
                    </li>
                `);
            });
        } else {
            $('#listaVeiculos').append(`
                <li class="list-group-item text-muted text-center py-3">
                    Nenhum veículo vinculado
                </li>
            `);
        }
    });


        // Loading nos botões
        $('.btn-excluir, .btn-toggle').on('click', function() {
            $(this).prop('disabled', true);
            $(this).html('<span class="spinner-border spinner-border-sm text-light"></span>');
            $(this).closest('form').submit();
        });

        // Fade-out da mensagem de sucesso
        const alert = document.getElementById("alert-success");
        if (alert) {
            setTimeout(() => {
                alert.classList.remove("show"); // inicia fade-out
                setTimeout(() => alert.remove(), 300); // remove após fade
            }, 3000);
        }
    });
</script>


@endpush
