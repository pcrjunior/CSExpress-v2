@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Card de título -->
        <div class="col-12 mb-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary">
                        <i class="fas fa-building me-2"></i>Gerenciamento de Filiais
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Filiais</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Card da tabela -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Lista de Filiais</h5>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#novaCnpjModal">
                        <i class="fas fa-plus me-1"></i>Nova Filial
                    </button>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="60">ID</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">CNPJ</th>
                                    <th scope="col">Endereço</th>
                                    <th scope="col">Cidade/UF</th>
                                    <th scope="col" width="150">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filiais as $filial)
                                <tr>
                                    <td>{{ $filial->id }}</td>
                                    <td><strong>{{ $filial->nome }}</strong></td>
                                    <td>{{ $filial->cnpj }}</td>
                                    <td>{{ $filial->endereco }}, {{ $filial->numero }}</td>
                                    <td>{{ $filial->cidade }}/{{ $filial->estado }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('filiais.edit', $filial) }}" class="btn btn-primary btn-sm me-2" title="Editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>

                                            <form action="{{ route('filiais.destroy', $filial) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                    <i class="fas fa-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                            <h5>Nenhuma filial encontrada</h5>
                                            <p class="text-muted">Clique em "Nova Filial" para cadastrar.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    @if(isset($filiais) && method_exists($filiais, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $filiais->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para inserir CNPJ -->
<div class="modal fade" id="novaCnpjModal" tabindex="-1" aria-labelledby="novaCnpjModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novaCnpjModalLabel">Informe o CNPJ da Nova Filial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNovaCnpj" action="{{ route('filiais.create') }}" method="GET">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cnpj" class="form-label">CNPJ</label>
                        <input type="text" class="form-control" id="cnpj" name="cnpj" placeholder="00.000.000/0000-00" required>
                        <div class="form-text">Digite o CNPJ sem pontuação ou com formatação padrão.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Continuar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
        border: 1px solid rgba(0,0,0,0.075);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.04);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa a máscara para CNPJ
    if (typeof $.fn.mask === 'function') {
        $('#cnpj').mask('00.000.000/0000-00', {reverse: true});
    }
    
    // Confirmação para deletar filial
    document.querySelectorAll('.delete-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!confirm('Tem certeza que deseja excluir esta filial?')) {
                event.preventDefault();
            }
        });
    });
    
    // Formulário já está configurado para enviar por GET para a rota filiais.create
    // Não precisa de JavaScript adicional para o botão Continuar
});
</script>
@endpush