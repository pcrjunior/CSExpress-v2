@extends('layouts.app')

@section('title', 'Editar Colaborador')

@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Editar Colaborador</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('entregadores.update', $entregador->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        {{-- Perfil --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="perfil" class="form-label">Perfil</label>
                                <select name="perfil" id="perfil" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="Motorista" {{ old('perfil', $entregador->perfil) == 'Motorista' ? 'selected' : '' }}>Motorista</option>
                                    <option value="Ajudante" {{ old('perfil', $entregador->perfil) == 'Ajudante' ? 'selected' : '' }}>Ajudante</option>
                                </select>
                            </div>
                        </div>

                        {{-- Nome e Telefone --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label for="nome" class="form-label">Nome</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $entregador->nome) }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="cpf" id="cpf" class="form-control" value="{{ old('cpf', $entregador->cpf) }}" placeholder="000.000.000-00">
                                </div>
                            </div>
                           
                            <div class="col-md-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>

                                    <input type="date" name="data_nascimento" id="data_nascimento" class="form-control"
                                    value="{{ old('data_nascimento', $entregador->data_nascimento ? \Carbon\Carbon::parse($entregador->data_nascimento)->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                            
                            <!-- Campos apenas para Motorista -->
                            <div class="dados-completos">
                                <!-- Informações Pessoais -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label for="telefone" class="form-label">Telefone</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', $entregador->telefone ?? '') }}">
                                        </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="email" class="form-label">E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa fa-envelope"></i></span>
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $entregador->email) }}">

                                    </div>
                                </div>
                            </div>
                            {{-- Dados do Motorista --}}
                            <div class="dados-completos">
                            

                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label for="cep" class="form-label">CEP</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="text" name="cep" id="cep" class="form-control" placeholder="00000-000" value="{{ old('cep', $entregador->cep) }}">
                                            <button type="button" class="btn btn-outline-secondary" id="buscarCep"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="endereco" class="form-label">Endereço</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-road"></i></span>
                                            <input type="text" name="endereco" id="endereco" class="form-control" value="{{ old('endereco', $entregador->endereco) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="numero" class="form-label">Número</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                                            <input type="text" name="numero" id="numero" class="form-control" value="{{ old('numero', $entregador->numero) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" name="complemento" id="complemento" class="form-control" value="{{ old('complemento', $entregador->complemento) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="bairro" class="form-label">Bairro</label>
                                        <input type="text" name="bairro" id="bairro" class="form-control" value="{{ old('bairro', $entregador->bairro) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" name="cidade" id="cidade" class="form-control" value="{{ old('cidade', $entregador->cidade) }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="estado" class="form-label">Estado</label>
                                        <input type="text" name="estado" id="estado" class="form-control" value="{{ old('estado', $entregador->estado) }}">
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <label for="cnh_numero" class="form-label">Número CNH</label>
                                        <input type="text" name="cnh_numero" id="cnh_numero" maxlength="11" class="form-control" value="{{ old('cnh_numero', $entregador->cnh_numero) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cnh_validade" class="form-label">Validade</label>
                                        <input type="date" name="cnh_validade" id="cnh_validade" class="form-control"
                                        value="{{ old('cnh_validade', $entregador->cnh_validade ? \Carbon\Carbon::parse($entregador->cnh_validade)->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cnh_categoria" class="form-label">Categoria</label>
                                        <input type="text" name="cnh_categoria" id="cnh_categoria" class="form-control" value="{{ old('cnh_categoria', $entregador->cnh_categoria) }}">
                                    </div>
                                </div>

                                <!-- <div class="mb-4">
                                    <label for="foto" class="form-label">Foto</label>
                                    <input type="file" name="foto" id="foto" class="form-control">
                                    @if($entregador->foto)
                                        <img src="{{ asset('storage/' . $entregador->foto) }}" class="img-thumbnail mt-2" style="max-height: 100px;">
                                    @endif
                                </div> -->
                            </div>

                            {{-- Veículos --}}
                            <div class="row mt-4 mb-4" id="secaoVeiculos" style="{{ old('perfil', $entregador->perfil) == 'Motorista' ? '' : 'display: none;' }}">
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">Veículos Atribuídos</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2 mb-3 align-items-end">
                                                <div class="col-md-2">
                                                    <label class="form-label">Filtrar por placa</label>
                                                    <input type="text" id="filtroPlaca" class="form-control" placeholder="Ex: ABC-1234">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Filtrar por fabricante</label>
                                                    <select id="filtroFabricante" class="form-select">
                                                        <option value="">Todos os fabricantes</option>
                                                        @foreach($veiculos->pluck('fabricante')->unique()->sort() as $fabricante)
                                                            <option value="{{ strtolower($fabricante) }}">{{ $fabricante }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Filtrar por modelo</label>
                                                    <input type="text" id="filtroModelo" class="form-control" placeholder="Ex: HR, 1113...">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end gap-2">
                                                    <button type="button" id="btnFiltrarVeiculos" class="btn btn-primary w-100">
                                                        <i class="fas fa-filter me-1"></i> Filtrar
                                                    </button>
                                                    <button type="button" id="btnLimparFiltros" class="btn btn-outline-secondary w-100">
                                                        <i class="fas fa-times me-1"></i> Limpar
                                                    </button>
                                                </div>
                                            </div>




                                            <div class="row" id="listaVeiculos" style="max-height: 300px; overflow-y: auto;">
                                                @foreach($veiculos as $veiculo)
                                                    <div class="col-md-6 mb-2 item-veiculo"
                                                        data-placa="{{ strtolower($veiculo->placa) }}"
                                                        data-fabricante="{{ strtolower($veiculo->fabricante) }}"
                                                        data-modelo="{{ strtolower($veiculo->modelo) }}">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="veiculos[]" id="veiculo_{{ $veiculo->id }}" value="{{ $veiculo->id }}"
                                                                class="form-check-input"
                                                                {{ in_array($veiculo->id, old('veiculos', $veiculosVinculados) ?: []) ? 'checked' : '' }}>
                                                            <label for="veiculo_{{ $veiculo->id }}" class="form-check-label">
                                                                <strong>{{ $veiculo->placa }}</strong> - {{ $veiculo->fabricante }} {{ $veiculo->modelo }} ({{ $veiculo->ano_modelo }})
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                <a href="{{ route('entregadores.index') }}" class="btn btn-outline-secondary">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#cep').mask('00000-000');
    $('#telefone').mask('(00) 00000-0000');
    $('#cpf').mask('000.000.000-00');

    $('#telefone').on('blur', function () {
        const phone = $(this).val().replace(/\D/g, '');
        $(this).mask(phone.length > 10 ? '(00) 00000-0000' : '(00) 0000-00009');
    });


    $('#cnh_numero').on('input', function () {
        this.value = this.value.replace(/\D/g, ''); // Remove tudo que não for número
    });

    function ajustarCamposPorPerfil() {
        const perfil = $('#perfil').val();
        $('.dados-completos').toggle(perfil === 'Motorista');
        $('#secaoVeiculos').toggle(perfil === 'Motorista');
    }

    $('#perfil').on('change', ajustarCamposPorPerfil);
    ajustarCamposPorPerfil();

    // FILTRO DE VEÍCULOS
    function filtrarVeiculos() {
        const placaFiltro = $('#filtroPlaca').val().toLowerCase().trim();
        const fabricanteFiltro = $('#filtroFabricante').val().toLowerCase().trim();
        const modeloFiltro = $('#filtroModelo').val().toLowerCase().trim();

        $('.item-veiculo').each(function () {
            const placa = ($(this).data('placa') || '').toString().toLowerCase();
            const fabricante = ($(this).data('fabricante') || '').toString().toLowerCase();
            const modelo = ($(this).data('modelo') || '').toString().toLowerCase();

            const placaOk = placa.includes(placaFiltro);
            const fabricanteOk = !fabricanteFiltro || fabricante === fabricanteFiltro;
            const modeloOk = modelo.includes(modeloFiltro);

            $(this).toggle(placaOk && fabricanteOk && modeloOk);
        });
    }

    function limparFiltros() {
        $('#filtroPlaca').val('');
        $('#filtroModelo').val('');
        $('#filtroFabricante').val('');
        $('.item-veiculo').show();
    }

    $('#btnFiltrarVeiculos').on('click', filtrarVeiculos);
    $('#btnLimparFiltros').on('click', limparFiltros);

    // Opcional: auto-filtragem ao digitar
    $('#filtroPlaca, #filtroModelo').on('input', filtrarVeiculos);
    $('#filtroFabricante').on('change', filtrarVeiculos);

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
                    $('#estado').val(data.uf);
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

    $('#cnh_categoria').on('input', function () {
        this.value = this.value.replace(/[^a-zA-Z]/g, '').toUpperCase(); // remove números e símbolos, força maiúsculo
    });

});
</script>
@endpush


@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
        border: 1px solid rgba(0, 0, 0, 0.075);
    }
    .form-control:focus, .input-group-text:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .text-uppercase {
        text-transform: uppercase;
    }
</style>
@endpush

