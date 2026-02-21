@extends('layouts.app')

@section('title', 'Editar Empresa')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">Editar Empresa</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('empresas.update', $empresa->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Os mesmos campos, agora com valores atuais -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ old('cnpj', $empresa->cnpj) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $empresa->nome) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="apelido" class="form-label">Apelido</label>
                                <input type="text" name="apelido" id="apelido" class="form-control" value="{{ old('apelido', $empresa->apelido) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $empresa->email) }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nome_contato" class="form-label">Nome do Contato</label>
                                <input type="text" name="nome_contato" id="nome_contato" class="form-control" value="{{ old('nome_contato', $empresa->nome_contato) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Número de Telefone</label>
                                <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', $empresa->telefone) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="logomarca" class="form-label">Logomarca</label>
                            @if($empresa->logomarca)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $empresa->logomarca) }}" alt="Logomarca" style="max-width: 120px;">
                                </div>
                            @endif
                            <input type="file" name="logomarca" id="logomarca" class="form-control">
                        </div>
                        <div class="d-flex justify-content-end">
                            <!-- <a href="{{ route('filiais.create', ['empresa_id' => $empresa->id, 'empresa_cnpj' => $empresa->cnpj]) }}" class="btn btn-success me-2 btn-sm">Adicionar Filial</a> -->
                            <button type="submit" class="btn btn-primary me-2 btn-sm">Atualizar</button>
                            <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
