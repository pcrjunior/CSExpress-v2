@extends('layouts.app')

@section('title', 'Novo Colaborador')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Cadastro de Colaborador</h5>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="card shadow-sm">
                <!-- <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Informações do Colaborador</h5>
                </div> -->
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

                    <form action="{{ route('entregadores.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <!-- Perfil -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="perfil" class="form-label">Perfil</label>
                                <select name="perfil" id="perfil" class="form-select" required>
                                    <option value="">Selecione</option>
                                    <option value="Motorista" {{ old('perfil') == 'Motorista' ? 'selected' : '' }}>Motorista</option>
                                    <option value="Ajudante" {{ old('perfil') == 'Ajudante' ? 'selected' : '' }}>Ajudante</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nome e Telefone -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label for="nome" class="form-label">Nome</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required>
                                </div>
                                <div class="invalid-feedback">Nome é obrigatório</div>
                            </div>
                            <div class="col-md-3">
                                <label for="cpf" class="form-label">CPF</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="cpf" id="cpf" class="form-control" value="{{ old('cpf') }}" required placeholder="000.000.000-00">
                                </div>
                                <div class="invalid-feedback">CPF é obrigatório</div>
                            </div>
                            <!-- <div class="col-md-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone') }}">
                                </div>
                            </div> -->
                            <div class="col-md-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" value="{{ old('data_nascimento') }}">
                                </div>
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
                                        <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone') }}">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label for="email" class="form-label">E-mail</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fa fa-envelope"></i></span>
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}">
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Endereço -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="cep" class="form-label">CEP</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" name="cep" id="cep" class="form-control" placeholder="00000-000" value="{{ old('cep') }}" maxlength="9">
                                        <button class="btn btn-outline-secondary" type="button" id="buscarCep">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="endereco" class="form-label">Endereço</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-road"></i></span>
                                        <input type="text" name="endereco" id="endereco" class="form-control" value="{{ old('endereco') }}" required>
                                    </div>
                                    <div class="invalid-feedback">Endereço é obrigatório</div>
                                </div>
                                <div class="col-md-2">
                                    <label for="numero" class="form-label">Número</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                                        <input type="text" name="numero" id="numero" class="form-control" value="{{ old('numero') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Complemento -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="complemento" class="form-label">Complemento</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-info-circle"></i></span>
                                        <input type="text" name="complemento" id="complemento" class="form-control" value="{{ old('complemento') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="bairro" class="form-label">Bairro</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-home"></i></span>
                                        <input type="text" name="bairro" id="bairro" class="form-control" value="{{ old('bairro') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="cidade" class="form-label">Cidade</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-city"></i></span>
                                        <input type="text" name="cidade" id="cidade" class="form-control" value="{{ old('cidade') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="estado" class="form-label">Estado</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-flag"></i></span>
                                        <input type="text" name="estado" id="estado" class="form-control" value="{{ old('estado') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- CNH -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label for="cnh_numero" class="form-label">Número da CNH</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-id-card"></i></span>
                                        <input type="text" name="cnh_numero" id="cnh_numero" maxlength="11" inputmode="numeric" pattern="\d*" class="form-control" value="{{ old('cnh_numero') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="cnh_validade" class="form-label">Validade da CNH</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" name="cnh_validade" id="cnh_validade" class="form-control" value="{{ old('cnh_validade') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="cnh_categoria" class="form-label">Categoria da CNH</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                                        <input type="text" name="cnh_categoria" id="cnh_categoria" class="form-control text-uppercase" inputmode="text" pattern="[A-Za-z]+" maxlength="3" value="{{ old('cnh_categoria') }}">
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Foto -->
                            <!-- <div class="mb-4">
                                <label for="foto" class="form-label">Foto do Motorista</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-image"></i></span>
                                    <input type="file" name="foto" id="foto" class="form-control">
                                </div>
                            </div> -->

                        </div> <!-- Fim dados-completos -->

                        <!-- Veículos (só para Motorista) -->
                        <div class="row mt-4 mb-4" id="secaoVeiculos" style="display: none;">
                            <!-- Conteúdo original dos veículos permanece aqui... -->
                            {{-- Mantenha tudo igual ao original --}}
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('entregadores.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmarCadastroModal">
                                <i class="fas fa-save me-1"></i>Salvar Colaborador
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmarCadastroModal" tabindex="-1" aria-labelledby="confirmarCadastroLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmação de Cadastro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Deseja realmente incluir este colaborador?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="confirmarSalvar">Cadastrar</button>
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

    $('#telefone').blur(function () {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length > 10) {
            $(this).mask('(00) 00000-0000');
        } else {
            $(this).mask('(00) 0000-00009');
        }
    });

    $('#cnh_numero').on('input', function () {
        this.value = this.value.replace(/\D/g, ''); // Remove tudo que não for número
    });

    function toggleVeiculosSection() {
        const perfil = $('#perfil').val();
        if (perfil === 'Motorista') {
            $('#secaoVeiculos').show();
        } else {
            $('#secaoVeiculos').hide();
        }
    }

    function ajustarCamposPorPerfil() {
        const perfil = $('#perfil').val();
        if (perfil === 'Ajudante') {
            $('.dados-completos').hide();
            $('#secaoVeiculos').hide();
        } else {
            $('.dados-completos').show();
            toggleVeiculosSection();
        }
    }

    $('#perfil').on('change', ajustarCamposPorPerfil);
    ajustarCamposPorPerfil();

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
        this.value = this.value.replace(/[^a-zA-Z]/g, '').toUpperCase();
    });

    // ✅ Correção do botão do modal
    document.getElementById('confirmarSalvar').addEventListener('click', function () {
        document.querySelector('form.needs-validation').submit();
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
