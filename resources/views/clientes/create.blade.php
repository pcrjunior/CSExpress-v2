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
                                        <label for="documento" id="labelDocumento" class="form-label">CPF / CNPJ</label>
                                        <input type="text" class="form-control @error('documento') is-invalid @enderror" id="documento" name="documento"
                                            value="{{ old('documento', isset($cliente) ? $cliente->documento : '') }}" required>
                                        @error('documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nome" id="labelNome" class="form-label">Nome / Razão Social</label>
                                        <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
                                            value="{{ old('nome', isset($cliente) ? $cliente->nome : '') }}" required>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
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

                        <!-- Endereço -->
                        <div class="card mb-4 ">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
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

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco"
                                            value="{{ old('endereco', isset($cliente) ? $cliente->endereco : '') }}" required>
                                        @error('endereco')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-2">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero"
                                            value="{{ old('numero', isset($cliente) ? $cliente->numero : '') }}" required>
                                        @error('numero')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control @error('complemento') is-invalid @enderror" id="complemento" name="complemento"
                                            value="{{ old('complemento', isset($cliente) ? $cliente->complemento : '') }}">
                                        @error('complemento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="bairro" class="form-label">Bairro</label>
                                        <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro" name="bairro"
                                            value="{{ old('bairro', isset($cliente) ? $cliente->bairro : '') }}" required>
                                        @error('bairro')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control @error('cidade') is-invalid @enderror" id="cidade" name="cidade"
                                            value="{{ old('cidade', isset($cliente) ? $cliente->cidade : '') }}" required>
                                        @error('cidade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
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

                        <!-- Contato -->
                        <div class="card mb-4">
                            <div class="card-header bg-header-blue d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-address-book me-2"></i>Responsáveis
                                </h6>
                                <button type="button" class="btn btn-sm btn-success ms-auto" id="btnAdicionarResponsavel">
                                    <i class="fas fa-plus me-1"></i>Adicionar Responsável
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="responsaveisContainer">
                                    @if(isset($cliente) && $cliente->responsaveis->count() > 0)
                                        @foreach($cliente->responsaveis as $index => $responsavel)
                                            <div class="responsavel-item mb-3 p-3 border rounded" data-responsavel-id="{{ $responsavel->id }}">
                                                <div class="row mb-2">
                                                    <div class="col-auto">
                                                        <span class="badge bg-primary">Responsável {{ $index + 1 }}</span>
                                                    </div>
                                                    @if(!$loop->first)
                                                        <div class="col-auto ms-auto">
                                                            <button type="button" class="btn btn-sm btn-danger btn-remover-responsavel">
                                                                <i class="fas fa-trash me-1"></i>Remover
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Nome</label>
                                                        <input type="text" class="form-control responsavel-nome" value="{{ $responsavel->nome }}" placeholder="Nome do responsável">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Telefone</label>
                                                        <input type="text" class="form-control responsavel-telefone" value="{{ $responsavel->telefone }}" placeholder="(99) 99999-9999">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">E-mail</label>
                                                        <input type="email" class="form-control responsavel-email" value="{{ $responsavel->email }}" placeholder="email@example.com">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="responsavel-item mb-3 p-3 border rounded" data-responsavel-id="new">
                                            <div class="row mb-2">
                                                <div class="col-auto">
                                                    <span class="badge bg-primary">Responsável 1</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="form-label">Nome</label>
                                                    <input type="text" class="form-control responsavel-nome" value="{{ old('responsavel', $cliente->responsavel ?? '') }}" placeholder="Nome do responsável" {{ !isset($cliente) ? 'required' : '' }}>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Telefone</label>
                                                    <input type="text" class="form-control responsavel-telefone" value="{{ old('telefone', $cliente->telefone ?? '') }}" placeholder="(99) 99999-9999">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">E-mail</label>
                                                    <input type="email" class="form-control responsavel-email" value="{{ old('email', $cliente->email ?? '') }}" placeholder="email@example.com" {{ !isset($cliente) ? 'required' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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

    // Gerenciar responsáveis dinâmicos
    $('#btnAdicionarResponsavel').on('click', function() {
        const container = $('#responsaveisContainer');
        const index = container.find('.responsavel-item').length + 1;
        
        const novoResponsavel = `
            <div class="responsavel-item mb-3 p-3 border rounded" data-responsavel-id="new">
                <div class="row mb-2">
                    <div class="col-auto">
                        <span class="badge bg-primary">Responsável ${index}</span>
                    </div>
                    <div class="col-auto ms-auto">
                        <button type="button" class="btn btn-sm btn-danger btn-remover-responsavel">
                            <i class="fas fa-trash me-1"></i>Remover
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control responsavel-nome" placeholder="Nome do responsável">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telefone</label>
                        <input type="text" class="form-control responsavel-telefone" placeholder="(99) 99999-9999">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">E-mail</label>
                        <input type="email" class="form-control responsavel-email" placeholder="email@example.com">
                    </div>
                </div>
            </div>
        `;
        
        container.append(novoResponsavel);
        aplicarMascaraTelefone();
    });

    // Remover responsável
    $(document).on('click', '.btn-remover-responsavel', function() {
        $(this).closest('.responsavel-item').remove();
    });

    // Aplicar máscara de telefone em responsáveis
    function aplicarMascaraTelefone() {
        $('.responsavel-telefone').mask('(00) 00000-0000');
        $('.responsavel-telefone').off('blur').on('blur', function() {
            var phone = $(this).val().replace(/\D/g, '');
            if (phone.length > 10) {
                $(this).mask('(00) 00000-0000');
            } else {
                $(this).mask('(00) 0000-00009');
            }
        });
    }

    // Aplicar máscara inicial aos responsáveis existentes
    aplicarMascaraTelefone();

    // Interceptar submissão do formulário
    $('form').on('submit', function(e) {
        const responsaveis = [];
        
        $('#responsaveisContainer .responsavel-item').each(function() {
            const nome = $(this).find('.responsavel-nome').val();
            const telefone = $(this).find('.responsavel-telefone').val();
            const email = $(this).find('.responsavel-email').val();
            const id = $(this).data('responsavel-id');
            
            if (nome || email) {
                responsaveis.push({
                    id: id,
                    nome: nome,
                    telefone: telefone,
                    email: email
                });
            }
        });

        // Remover campo anterior se existir
        $(this).find('input[name="responsaveis_data"]').remove();

        // Armazenar dados em campo oculto
        $('<input>').attr({
            type: 'hidden',
            name: 'responsaveis_data',
            value: JSON.stringify(responsaveis)
        }).appendTo(this);
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
