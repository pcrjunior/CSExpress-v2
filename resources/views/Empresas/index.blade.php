@extends('layouts.app')

@section('title', 'Gerenciamento de Empresas')

@section('content')
<div class="container-fluid py-4">
    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Empresas</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('empresas.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Nova Empresa
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
                    <h5 class="mb-0">Pesquisar Empresas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('empresas.index') }}" method="GET" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ request('cnpj') }}" placeholder="Digite o CNPJ" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <label for="nome" class="form-label">Nome</label>
                                <input type="text" name="nome" id="nome" class="form-control" value="{{ request('nome') }}" placeholder="Digite o nome" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <label for="apelido" class="form-label">Nome Fantasia</label>
                                <input type="text" name="apelido" id="apelido" class="form-control" value="{{ request('apelido') }}" placeholder="Digite o nome fantasia" autocomplete="off">
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-custom-blue btn-sm">
                                <i class="fas fa-search me-1"></i> Pesquisar
                            </button>
                            <a href="{{ route('empresas.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-eraser me-1"></i> Limpar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Empresas -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive small-table">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-header-blue">
                        <tr>
                            <th>CNPJ</th>
                            <th>Nome</th>
                            <th>Nome Fantasia</th>
                            <th>E-mail</th>
                            <th>Contato</th>
                            <th>Telefone</th>
                            <th>Logomarca</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empresas as $empresa)
                            <tr>
                                <td>{{ $empresa->cnpj }}</td>
                                <td><strong>{{ $empresa->nome }}</strong></td>
                                <td>{{ $empresa->apelido }}</td>
                                <td>
                                    @if($empresa->email)
                                        <a href="mailto:{{ $empresa->email }}">{{ $empresa->email }}</a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </td>
                                <td>{{ $empresa->nome_contato ?? 'Não informado' }}</td>
                                <td>
                                    @if($empresa->telefone)
                                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $empresa->telefone) }}">{{ $empresa->telefone }}</a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($empresa->logomarca)
                                        <img src="{{ asset('storage/' . $empresa->logomarca) }}" alt="Logomarca" class="img-thumbnail" style="max-width: 60px; max-height: 40px;">
                                    @else
                                        <span class="text-muted"><i class="fas fa-image"></i> Sem imagem</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('empresas.edit', $empresa->id) }}" class="btn btn-action btn-action-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('empresas.destroy', $empresa->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-action btn-action-delete btn-excluir" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <div>Nenhuma empresa encontrada.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if(isset($empresas) && method_exists($empresas, 'links'))
                <div class="d-flex justify-content-center mt-4">
                    {{ $empresas->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-excluir').on('click', function() {
            $(this).prop('disabled', true);
            $(this).html('<span class="spinner-border spinner-border-sm text-light"></span>');
            $(this).closest('form').submit();
        });
    });
</script>
@endpush
