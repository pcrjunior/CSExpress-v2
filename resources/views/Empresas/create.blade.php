@extends('layouts.app')

@section('title', 'Nova Empresa')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Card de título -->
        <div class="col-12 mb-4">
            <div class="card bg-light shadow-sm">
                <div class="card-header bg-header-blue">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Cadastro de Empresa</h5>
                </div>
            </div>
        </div>

        <!-- Card para o formulário -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">Informações da Empresa</h5>
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

                    <form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Dados principais -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="cnpj" class="form-label">CNPJ</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ old('cnpj') }}" placeholder="00.000.000/0000-00" required>
                                </div>
                                <div class="invalid-feedback">CNPJ é obrigatório</div>
                            </div>
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-building"></i></span>
                                    <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome') }}" required>
                                </div>
                                <div class="invalid-feedback">Nome é obrigatório</div>
                            </div>
                        </div>

                        <!-- Informações adicionais -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="apelido" class="form-label">Nome fantasia</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-tag"></i></span>
                                    <input type="text" name="apelido" id="apelido" class="form-control" value="{{ old('apelido') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="empresa@exemplo.com">
                                </div>
                            </div>
                        </div>

                        <!-- Contato -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nome_contato" class="form-label">Nome do Contato</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    <input type="text" name="nome_contato" id="nome_contato" class="form-control" value="{{ old('nome_contato') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone') }}" placeholder="(00) 00000-0000">
                                </div>
                            </div>
                        </div>

                        <!-- Upload de arquivo -->
                        <div class="mb-4">
                            <label for="logomarca" class="form-label">Logomarca</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-image"></i></span>
                                <input type="file" name="logomarca" id="logomarca" class="form-control" accept="image/*">
                            </div>
                            <small class="text-muted">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar Empresa
                            </button>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Inicializar máscara para o CNPJ
    $(document).ready(function() {
        $('#cnpj').mask('00.000.000/0000-00');
        $('#telefone').mask('(00) 00000-0000');
        
        // Bootstrap validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    });
</script>
@endpush

@push('styles')
<style>
    .card {
        border-radius: 0.5rem;
        border: 1px solid rgba(0,0,0,0.075);
    }
    .form-control:focus, .input-group-text:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .input-group-text {
        border-right: none;
    }
    .input-group .form-control {
        border-left: none;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush
@endsection