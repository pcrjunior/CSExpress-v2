@extends('layouts.app')

@section('title', 'Gerenciamento de Usuários')

@section('content')
<div class="container-fluid py-4">
    <!-- Mensagem de sucesso -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Mensagem de Erro -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Usuários do Sistema</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>    

    <!-- Filtro -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm small-table">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0">Pesquisar Usuários</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('usuarios.index') }}" method="GET" class="needs-validation" novalidate>
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
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="desabilitado" {{ request('status') == 'desabilitado' ? 'selected' : '' }}>Desabilitado</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-custom-blue btn-sm">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary btn-sm">
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
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario->name }}</td>
                                    <td>{{ $usuario->email }}</td>
                                    <td>
                                        <span class="badge {{ $usuario->active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $usuario->active ? 'Ativo' : 'Desabilitado' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-action btn-action-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('usuarios.toggle', $usuario->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-action btn-action-toggle btn-toggle" title="{{ $usuario->active ? 'Desabilitar' : 'Habilitar' }}">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-action btn-action-delete btn-excluir" title="Excluir" data-id="{{ $usuario->id }}" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h5>Nenhum usuário encontrado</h5>
                                            <p class="text-muted">Clique em "Novo Usuário" para cadastrar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                @if(isset($usuarios) && method_exists($usuarios, 'links'))
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <span class="text-muted">
                                Exibindo {{ $usuarios->firstItem() ?? 0 }} a {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() ?? 0 }} resultados
                            </span>
                        </div>
                        <div>
                            {{ $usuarios->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este usuário?</p>
                <p><strong>Esta ação não poderá ser desfeita.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Confirmar Exclusão</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Máscara para CPF (se quiser aplicar)
    $('#cpf').mask('000.000.000-00');

    // Modal Exclusão - Captura ID
    $('.btn-excluir').on('click', function() {
        const id = $(this).data('id');
        $('#formExcluir').attr('action', `/usuarios/${id}`);
    });

    // Loading nos botões de toggle
    $('.btn-toggle').on('click', function() {
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $(this).closest('form').submit();
    });

});

document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    });



</script>
@endpush
@endsection
