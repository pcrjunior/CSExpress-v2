@extends('layouts.app')

@section('title', isset($cliente) ? 'Editar Cliente' : 'Novo Cliente')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Card de título -->
        <div class="col-12 mb-4 ">
            <div class="card shadow-sm ">
                <div class="card bg-header-blue">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>{{ isset($cliente) ? 'Editar' : 'Novo' }} Cliente
                    </h4>
                   <!--  <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ isset($cliente) ? 'Editar' : 'Novo' }}</li>
                        </ol>
                    </nav> -->
                </div>
            </div>
        </div>

        <!-- Card do formulário -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Formulário de Cliente</h5>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>

                <div class="card-body ">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-1"></i>Por favor, corrija os erros abaixo:
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($cliente) ? route('clientes.update', $cliente) : route('clientes.store') }}">
                        @csrf
                        @if(isset($cliente))
                            @method('PUT')
                        @endif

                        <!-- Tipo de Cliente -->
                        <div class="card mb-4">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-id-badge me-2"></i>Tipo de Cliente</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo" id="tipoPF" value="PF"
                                                {{ old('tipo', isset($cliente) ? $cliente->tipo : '') == 'PF' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="tipoPF">Pessoa Física</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipo" id="tipoPJ" value="PJ"
                                                {{ old('tipo', isset($cliente) ? $cliente->tipo : '') == 'PJ' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="tipoPJ">Pessoa Jurídica</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Principais -->
                        <div class="card mb-4">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Dados Principais</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="documento" id="labelDocumento" class="form-label">CPF / CNPJ</label>
                                            <input type="text" class="form-control @error('documento') is-invalid @enderror" id="documento" name="documento"
                                                value="{{ old('documento', isset($cliente) ? $cliente->documento : '') }}" required>
                                            @error('documento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nome" id="labelNome" class="form-label">Nome / Razão Social</label>
                                            <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
                                                value="{{ old('nome', isset($cliente) ? $cliente->nome : '') }}" required>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="apelido" class="form-label">Apelido / Nome Fantasia</label>
                                            <input type="text" class="form-control @error('apelido') is-invalid @enderror" id="apelido" name="apelido"
                                                value="{{ old('apelido', isset($cliente) ? $cliente->apelido : '') }}">
                                            @error('apelido')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="card mb-4 ">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="cep" class="form-label">CEP</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep"
                                                    value="{{ old('cep', isset($cliente) ? $cliente->cep : '') }}" required>
                                                <button class="btn btn-outline-secondary" type="button" id="buscarCep">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                                @error('cep')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group ">
                                            <label for="endereco" class="form-label">Endereço</label>
                                            <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco"
                                                value="{{ old('endereco', isset($cliente) ? $cliente->endereco : '') }}" required>
                                            @error('endereco')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="numero" class="form-label">Número</label>
                                            <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero"
                                                value="{{ old('numero', isset($cliente) ? $cliente->numero : '') }}" required>
                                            @error('numero')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="complemento" class="form-label">Complemento</label>
                                            <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento" name="complemento"
                                                value="{{ old('complemento', isset($cliente) ? $cliente->complemento : '') }}">
                                            @error('complemento')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bairro" class="form-label">Bairro</label>
                                            <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro" name="bairro"
                                                value="{{ old('bairro', isset($cliente) ? $cliente->bairro : '') }}"
                                            @error('bairro')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cidade" class="form-label">Cidade</label>
                                            <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade"
                                                value="{{ old('cidade', isset($cliente) ? $cliente->cidade : '') }}" required>
                                            @error('cidade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="uf" class="form-label">UF</label>
                                            <select class="form-select @error('uf') is-invalid @enderror" id="uf" name="uf" required>
                                                <option value="">Selecione</option>
                                                @foreach(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'] as $estado)
                                                    <option value="{{ $estado }}" {{ old('uf', isset($cliente) ? $cliente->uf : '') == $estado ? 'selected' : '' }}>
                                                        {{ $estado }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('uf')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contato -->
                        <div class="card mb-4">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0">
                                    <i class="fas fa-address-book me-2"></i>Contato
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- Responsável -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="responsavel" class="form-label">Responsável</label>
                                            <input
                                                type="text"
                                                class="form-control @error('responsavel') is-invalid @enderror"
                                                id="responsavel"
                                                name="responsavel"
                                                value="{{ old('responsavel', $cliente->responsavel ?? '') }}"
                                            >
                                            @error('responsavel')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Telefone -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="telefone" class="form-label">Telefone</label>
                                            <input
                                                type="text"
                                                class="form-control @error('telefone') is-invalid @enderror"
                                                id="telefone"
                                                name="telefone"
                                                placeholder="(99) 99999-9999 ou (99) 9999-9999"
                                                value="{{ old('telefone', $cliente->telefone ?? '') }}"
                                            >
                                            @error('telefone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- E-mail -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email" class="form-label">E-mail</label>
                                            <input
                                                type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                id="email"
                                                name="email"
                                                value="{{ old('email', $cliente->email ?? '') }}"
                                                required
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <!-- Responsável -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="responsavel2" class="form-label">Responsável 2</label>
                                            <input
                                                type="text"
                                                class="form-control @error('responsavel2') is-invalid @enderror"
                                                id="responsavel2"
                                                name="responsavel2"
                                                value="{{ old('responsavel2', $cliente->responsavel2 ?? '') }}"
                                            >
                                            @error('responsavel2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Telefone -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="telefone2" class="form-label">Telefone 2</label>
                                            <input
                                                type="text"
                                                class="form-control @error('telefone2') is-invalid @enderror"
                                                id="telefone2"
                                                name="telefone2"
                                                placeholder="(99) 99999-9999 ou (99) 9999-9999"
                                                value="{{ old('telefone2', $cliente->telefone2 ?? '') }}"
                                            >
                                            @error('telefone2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- E-mail -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email2" class="form-label">E-mail 2</label>
                                            <input
                                                type="email"
                                                class="form-control @error('email2') is-invalid @enderror"
                                                id="email2"
                                                name="email2"
                                                value="{{ old('email2', $cliente->email2 ?? '') }}"
                                            >
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function () {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery não está carregado!');
        return;
    }

    // CPF/CNPJ dinâmico
    function setDocumentoMask() {
        if ($('#tipoPF').is(':checked')) {
            $('#labelDocumento').text('CPF');
            $('#documento').mask('000.000.000-00');
        } else {
            $('#labelDocumento').text('CNPJ');
            $('#documento').mask('00.000.000/0000-00');
        }
    }


    setDocumentoMask();

    $('input[name="tipo"]').change(setDocumentoMask);

    // Máscara de CEP
    $('#cep').mask('00000-000');

    // Máscara dinâmica de telefone (fixo ou celular)
    $('#telefone').mask('(00) 00000-0000');

    $('#telefone').blur(function () {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 10) {
            $(this).mask('(00) 00000-0000'); // celular
        } else {
            $(this).mask('(00) 0000-00009'); // fixo com fallback de celular
        }
    });

        // Máscara dinâmica de telefone (fixo ou celular)
    $('#telefone2').mask('(00) 00000-0000');

    $('#telefone2').blur(function () {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 10) {
            $(this).mask('(00) 00000-0000'); // celular
        } else {
            $(this).mask('(00) 0000-00009'); // fixo com fallback de celular
        }
    });

    // Buscar CEP via ViaCEP
    function buscarCep() {
        var cep = $('#cep').val().replace(/\D/g, '');
        if (cep.length !== 8) {
            alert('CEP inválido');
            return;
        }

        $('#buscarCep').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $('#endereco, #complemento, #bairro, #cidade').val('');
        $('#uf').val('');

        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`)
            .done(function (data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#uf').val(data.uf);
                    $('#numero').focus();
                } else {
                    alert('CEP não encontrado');
                }
            })
            .fail(function () {
                alert('Erro ao buscar CEP');
            })
            .always(function () {
                $('#buscarCep').prop('disabled', false).html('<i class="fas fa-search"></i>');
            });
    }

    $('#buscarCep').on('click', function (e) {
        e.preventDefault();
        buscarCep();
    });

    $('#cep').on('blur', function () {
        if ($(this).val().replace(/\D/g, '').length === 8) {
            buscarCep();
        }
    });
});
</script>

@endpush


@section('styles')
<style>
    .card {
        border-radius: 0.5rem;
        border: 1px solid rgba(0,0,0,0.075);
    }

    .card-header {
        border-radius: calc(0.5rem - 1px) calc(0.5rem - 1px) 0 0 !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
</style>
@endsection
