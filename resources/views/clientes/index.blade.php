@extends('layouts.app')

@section('title', 'Gerenciamento de Clientes')

@section('content')
<div class="container-fluid py-4">
    <!-- Título -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Clientes</h5>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <a href="{{ route('clientes.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Cliente
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
                    <h5 class="mb-0">Pesquisar Clientes</h5>
                </div>
                <div class="card-body">
                <form method="GET" action="{{ route('clientes.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label>CPF/CNPJ</label>
                                <input type="text" name="documento" id="documento" class="form-control" value="{{ request('documento') }}" placeholder="Digite o CPF ou CNPJ">
                            </div>

                            <div class="col-md-3">
                                <label>Nome</label>
                                <input type="text" name="nome" class="form-control" value="{{ request('nome') }}" placeholder="Digite o nome do cliente">
                            </div>

                            <div class="col-md-3">
                                <label>Apelido</label>
                                <input type="text" name="apelido" class="form-control" value="{{ request('apelido') }}" placeholder="Digite o apelido">
                            </div>

                            <div class="col-md-3">
                                <label>Telefone</label>
                                <input type="text" name="telefone" id="telefone" class="form-control" value="{{ request('telefone') }}" placeholder="Digite o telefone">
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Pesquisar
                            </button>
                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Limpar Filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagem de Sucesso -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="alert-success">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabela -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive small-table">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-header-blue">
                        <tr>
                            <th>CPF/CNPJ</th>
                            <th>Nome</th>
                            <th>Apelido</th>
                            <th>Telefone</th>
                            <th>E-mail</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->documento_formatado }}</td>
                                <td>{{ $cliente->nome }}</td>
                                <td>{{ $cliente->apelido }}</td>
                                <td>
                                    @if($cliente->telefone)
                                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $cliente->telefone) }}">{{ $cliente->telefone }}</a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cliente->email)
                                        <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-action btn-action-view" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-action btn-action-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline">
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
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <div>Nenhum cliente encontrado.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


        </div>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            Exibindo {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} resultados
        </div>
        <div>
            {{ $clientes->links() }}
        </div>
    </div>

</div>


<!-- Modal de confirmação -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o cliente <strong id="clienteNome"></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Sim, excluir</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="{{ asset('js/jquery/imask.js') }}"></script>
             
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const documentoInput = document.getElementById('documento');

        if (documentoInput) {
            const maskOptions = {
                mask: [
                    {
                        mask: '000.000.000-00',
                        regex: /^\d{0,11}$/,
                        lazy: false
                    },
                    {
                        mask: '00.000.000/0000-00',
                        regex: /^\d{0,14}$/,
                        lazy: false
                    }
                ],
                dispatch: function (appended, dynamicMasked) {
                    const number = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return number.length > 11 ? dynamicMasked.compiledMasks[1] : dynamicMasked.compiledMasks[0];
                }
            };

            IMask(documentoInput, maskOptions);
        }

        // Máscara de telefone (usando jQuery Mask ainda)
        var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length === 11
                ? '(00) 00000-0000'
                : '(00) 0000-00009';
        };

        $('#telefone').mask(SPMaskBehavior, {
            onKeyPress: function (val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            }
        });
    });
</script>

@endpush


