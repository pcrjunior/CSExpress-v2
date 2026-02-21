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
        <div class="col-md-6 mb-4">
            
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas  me-2"></i>Informações Gerais</h5>
                </div>
                <div class="card-body small">
                    <p><strong>Tipo:</strong> {{ $cliente->tipo == 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica' }}</p>
                    <p><strong>{{ $cliente->tipo == 'PF' ? 'CPF' : 'CNPJ' }}:</strong> {{ $cliente->documento_formatado }}</p>
                    <p><strong>{{ $cliente->tipo == 'PF' ? 'Nome' : 'Razão Social' }}:</strong> {{ $cliente->nome }}</p>
                    <p><strong>{{ $cliente->tipo == 'PF' ? 'Apelido' : 'Apelido' }}:</strong> {{ $cliente->apelido ?? 'Não informado' }}</p>
                    <br>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas  me-2"></i>Contato</h5>
                </div>
                <div class="card-body">
                     <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Responsável:</strong></p>
                            <p>{{ $cliente->responsavel ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Telefone:</strong></p>
                            <p>{{ $cliente->telefone ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>E-mail:</strong></p>
                            <p>{{ $cliente->email ?? 'Não informado' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Responsável 2:</strong></p>
                            <p>{{ $cliente->responsavel2 ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Telefone 2:</strong></p>
                            <p>{{ $cliente->telefone2 ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>E-mail 2:</strong></p>
                            <p>{{ $cliente->email2 ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas  me-2"></i>Endereço</h5>
                </div>
                <div class="card-body small">
                    <p><strong>CEP:</strong> {{ $cliente->cep_formatado }}</p>
                    <p><strong>Endereço:</strong> {{ $cliente->endereco }}, {{ $cliente->numero }} {{ $cliente->complemento ? ', ' . $cliente->complemento : '' }}</p>
                    <p><strong>Cidade/UF:</strong> {{ $cliente->cidade }} - {{ $cliente->uf }}</p>
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
