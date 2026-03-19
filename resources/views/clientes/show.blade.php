@extends('layouts.app')

@section('title', 'Detalhes do Cliente')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-xs">
                    <div class="card-header bg-header-blue">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Informações do Cliente</h5>
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                         
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        

    <!-- Card Título -->
   <!--  <div class="row mb-4">
        <div class="col-12">
             <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Empresas</h5>
                </div>
                </div>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> -->


            <!-- <div class="card shadow-xs">
                <div class="card shadow-xs">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Empresas</h5>
                </div>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div> 
        </div>
    </div> -->

    <!-- Dados do Cliente -->
    <div class="row">
        <div class="col-md-12 mb-4">
            
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas  me-2"></i>Informações Gerais</h5>
                </div>
                <div class="card-body small">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tipo:</strong> {{ $cliente->tipo == 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
                            <p><strong>{{ $cliente->tipo == 'PF' ? 'CPF' : 'CNPJ' }}:</strong> {{ $cliente->documento_formatado }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ $cliente->tipo == 'PF' ? 'Nome' : 'Razão Social' }}:</strong> {{ $cliente->nome }}</p>
                            <p><strong>{{ $cliente->tipo == 'PF' ? 'Apelido' : 'Apelido' }}:</strong> {{ $cliente->apelido ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Responsáveis <span class="badge bg-dark ms-2">{{ $cliente->responsaveis->count() }}</span></h5>
                </div>
                <div class="card-body">
                    @if($cliente->responsaveis->count() > 0)
                        <div class="row">
                            @foreach($cliente->responsaveis as $index => $responsavel)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            <h6 class="mb-0">{{ $responsavel->nome }}</h6>
                                        </div>
                                        <div class="ps-4">
                                            @if($responsavel->telefone)
                                                <p class="mb-2 small">
                                                    <i class="fas fa-phone text-success me-2"></i>
                                                    <strong>Tel:</strong> {{ $responsavel->telefone }}
                                                </p>
                                            @endif
                                            @if($responsavel->email)
                                                <p class="mb-0 small">
                                                    <i class="fas fa-envelope text-info me-2"></i>
                                                    <strong>Email:</strong> <a href="mailto:{{ $responsavel->email }}">{{ $responsavel->email }}</a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>Nenhum responsável cadastrado.
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h5>
                </div>
                <div class="card-body small">
                    <div class="row">
                        <div class="col-md-3">
                            <p><strong>CEP:</strong> {{ $cliente->cep_formatado }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Endereço:</strong> {{ $cliente->endereco }}, {{ $cliente->numero }}</p>
                        </div>
                        <div class="col-md-3">
                            <p><strong>Complemento:</strong> {{ $cliente->complemento ?? '-' }}</p>
                        </div>
                        <div class="col-md-2">
                            <p><strong>Bairro:</strong> {{ $cliente->bairro ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <p><strong>Cidade/UF:</strong> {{ $cliente->cidade }} - {{ $cliente->uf }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas  me-2"></i>Informações de Registro</h5>
                </div>
                <div class="card-body small">
                    <p><strong>Cadastrado em:</strong> {{ $cliente->created_at ? $cliente->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    <p><strong>Última atualização:</strong> {{ $cliente->updated_at ? $cliente->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- <div class="col-md-12 mb-4">
            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i> Excluir Cliente
                </button>
            </form>
        </div> -->
    </div>
</div>
@endsection
