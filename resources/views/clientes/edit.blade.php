@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card bg-header-blue">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Editar Cliente
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Formulário de Cliente</h5>
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>

                <div class="card-body">
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

                    <form method="POST" action="{{ route('clientes.update', $cliente) }}">

                        @csrf
                        @method('PUT')

                        <input type="hidden" id="tipo" name="tipo" value="{{ old('tipo', $cliente->tipo) }}">


                        <!-- Tipo -->
                        <div class="card mb-4">
                            <!-- <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-id-badge me-2"></i>Tipo de Cliente</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipoPF" value="PF" {{ old('tipo', $cliente->tipo) == 'PF' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipoPF">Pessoa Física</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipoPJ" value="PJ" {{ old('tipo', $cliente->tipo) == 'PJ' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipoPJ">Pessoa Jurídica</label>
                                </div>
                            </div>
                        </div> -->

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
                                            value="{{ old('documento', $cliente->documento) }}" required>
                                        @error('documento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nome" class="form-label">Nome / Razão Social</label>
                                        <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
                                            value="{{ old('nome', $cliente->nome) }}" required>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apelido" class="form-label">Apelido / Nome Fantasia</label>
                                        <input type="text" class="form-control @error('apelido') is-invalid @enderror" id="apelido" name="apelido"
                                            value="{{ old('apelido', $cliente->apelido) }}">
                                        @error('apelido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="card mb-4">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Endereço</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="cep" class="form-label">CEP</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('cep') is-invalid @enderror" id="cep" name="cep"
                                                value="{{ old('cep', $cliente->cep) }}" required>
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
                                            value="{{ old('endereco', $cliente->endereco) }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control @error('numero') is-invalid @enderror" id="numero" name="numero"
                                            value="{{ old('numero', $cliente->numero) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control" id="complemento" name="complemento"
                                            value="{{ old('complemento', $cliente->complemento) }}">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="bairro" class="form-label">Bairro</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro"
                                            value="{{ old('bairro', $cliente->bairro) }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade"
                                            value="{{ old('cidade', $cliente->cidade) }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="uf" class="form-label">UF</label>
                                        <select class="form-select" id="uf" name="uf" required>
                                            <option value="">Selecione</option>
                                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $estado)
                                                <option value="{{ $estado }}" {{ old('uf', $cliente->uf) == $estado ? 'selected' : '' }}>
                                                    {{ $estado }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contato -->
                        <div class="card mb-4">
                            <div class="card-header bg-header-blue">
                                <h6 class="mb-0"><i class="fas fa-address-book me-2"></i>Contato</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="responsavel" class="form-label">Responsável</label>
                                        <input type="text" class="form-control" id="responsavel" name="responsavel"
                                            value="{{ old('responsavel', $cliente->responsavel) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <input type="text" class="form-control" id="telefone" name="telefone"
                                            placeholder="(99) 99999-9999 ou (99) 9999-9999"
                                            value="{{ old('telefone', $cliente->telefone) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $cliente->email) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ações -->
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


<script src="{{ asset('js/jquery/imask.js') }}"></script>
<script>
$(document).ready(function () {
    $('input[name="tipo"]').change(setDocumentoMask);
    $('#cep').mask('00000-000');
    $('#telefone').mask('(00) 00000-0000');

    $('#telefone').blur(function () {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 10) {
            $(this).mask('(00) 00000-0000');
        } else {
            $(this).mask('(00) 0000-00009');
        }
    });

    $('#buscarCep').on('click', function () {
        const cep = $('#cep').val().replace(/\D/g, '');
        if (cep.length !== 8) return alert('CEP inválido');

        $('#buscarCep').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`)
            .done(function (data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#complemento').val(data.complemento);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#uf').val(data.uf);
                } else {
                    alert('CEP não encontrado');
                }
            })
            .fail(() => alert('Erro ao buscar CEP'))
            .always(() => $('#buscarCep').prop('disabled', false).html('<i class="fas fa-search"></i>'));
    });

    $('#cep').on('blur', function () {
        if ($(this).val().replace(/\D/g, '').length === 8) $('#buscarCep').click();
    });


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


    // Fecha automaticamente os alerts após 5 segundos
    setTimeout(function() {
        document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
            let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 5000);

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
