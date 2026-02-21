@extends('layouts.app')

@section('title', 'Editar Filial')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Card de título -->
        <div class="col-12 mb-4">
            <div class="card bg-light shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary">
                        <i class="fas fa-building me-2"></i>Editar Filial
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('filiais.index') }}">Filiais</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Card do formulário -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Informações da Filial</h5>
                </div>
                
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-1"></i>Por favor, corrija os erros abaixo:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('filiais.update', $filial) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Exibição fixa do CNPJ da Empresa -->
                            <div class="col-md-6 mb-3">
                                <label for="empresa_cnpj" class="form-label">CNPJ da Empresa</label>
                                <input type="text" id="empresa_cnpj" class="form-control" value="{{ $filial->empresa->cnpj ?? '' }}" disabled>
                                <input type="hidden" name="empresa_id" value="{{ $filial->empresa_id }}">
                            </div>

                            <!-- Nome da Filial -->
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome da Filial</label>
                                <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $filial->nome) }}" required>
                            </div>

                            <!-- CNPJ da Filial -->
                            <div class="col-md-6 mb-3">
                                <label for="cnpj" class="form-label">CNPJ da Filial</label>
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ old('cnpj', $filial->cnpj) }}" required>
                            </div>

                            <!-- CEP -->
                            <div class="col-md-6 mb-3">
                                <label for="cep" class="form-label">CEP</label>
                                <input type="text" name="cep" id="cep" class="form-control" value="{{ old('cep', $filial->cep) }}" required>
                            </div>

                            <!-- Endereço -->
                            <div class="col-md-8 mb-3">
                                <label for="endereco" class="form-label">Endereço</label>
                                <input type="text" name="endereco" id="endereco" class="form-control" value="{{ old('endereco', $filial->endereco) }}" required>
                            </div>

                            <!-- Número -->
                            <div class="col-md-4 mb-3">
                                <label for="numero" class="form-label">Número</label>
                                <input type="text" name="numero" id="numero" class="form-control" value="{{ old('numero', $filial->numero) }}" required>
                            </div>

                            <!-- Complemento -->
                            <div class="col-md-4 mb-3">
                                <label for="complemento" class="form-label">Complemento</label>
                                <input type="text" name="complemento" id="complemento" class="form-control" value="{{ old('complemento', $filial->complemento) }}">
                            </div>

                            <!-- Bairro -->
                            <div class="col-md-8 mb-3">
                                <label for="bairro" class="form-label">Bairro</label>
                                <input type="text" name="bairro" id="bairro" class="form-control" value="{{ old('bairro', $filial->bairro) }}" required>
                            </div>

                            <!-- Cidade -->
                            <div class="col-md-8 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" name="cidade" id="cidade" class="form-control" value="{{ old('cidade', $filial->cidade) }}" required>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-4 mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <input type="text" name="estado" id="estado" class="form-control" value="{{ old('estado', $filial->estado) }}" maxlength="2" required>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('filiais.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
        border: 1px solid rgba(0,0,0,0.075);
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    .alert {
        border-radius: 0.5rem;
    }
</style>
@endpush
@endsection