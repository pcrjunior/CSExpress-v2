@extends('layouts.app')

@section('title', 'Novo Veículo')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-header-blue">
                    <h4 class="mb-0">Cadastro de Veículo</h4>
                </div>
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

                    <form action="{{ route('veiculos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Placa, Fabricante e Modelo -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="placa" class="form-label">Placa</label>
                                <input type="text" name="placa" id="placa" class="form-control text-uppercase"
                                    value="{{ old('placa') }}" required maxlength="8"
                                    placeholder="AAA-9999 ou AAA-9A99" autocomplete="off">
                            </div>
                            <div class="col-md-4">
                                <label for="fabricante" class="form-label">Fabricante</label>
                                <select name="fabricante" id="fabricante" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($fabricantes as $fabricante)
                                        <option value="{{ $fabricante->nome }}" {{ old('fabricante') == $fabricante->nome ? 'selected' : '' }}>
                                            {{ $fabricante->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo</label>
                                <input type="text" name="modelo" id="modelo" class="form-control" value="{{ old('modelo') }}" required>
                            </div>
                            
                        </div>

                        <!-- Ano de Fabricação, Ano Modelo, Categoria e Cor -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="ano_fabricacao" class="form-label">Ano de Fabricação</label>
                                <input type="number" name="ano_fabricacao" id="ano_fabricacao" class="form-control" value="{{ old('ano_fabricacao') }}" 
                                 required maxlength="4" pattern="\d{4}" inputmode="numeric" placeholder="Ex: 2024" required maxlength="4">
                            </div>
                            <div class="col-md-3">
                                <label for="ano_modelo" class="form-label">Anos Modelo</label>
                                <input type="number" name="ano_modelo" id="ano_modelo" class="form-control" value="{{ old('ano_modelo') }}" 
                                required maxlength="4" pattern="\d{4}" inputmode="numeric" placeholder="Ex: 2025" required maxlength="4">
                            </div>
                            <div class="col-md-3">
                                <label for="categoria" class="form-label">Categoria</label>
                                <select name="categoria" id="categoria" class="form-select" required>
                                    <option value="">Selecione</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria }}" {{ old('categoria', $veiculo->categoria ?? '') == $categoria ? 'selected' : '' }}>
                                            {{ $categoria }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cor" class="form-label">Cor</label>
                                <input type="text" name="cor" id="cor" class="form-control" value="{{ old('cor') }}" required>
                            </div>
                        </div>

                        <!-- Rodízio e Dia do Rodízio -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rodizio" class="form-label">Rodízio</label>
                                <select name="rodizio" id="rodizio" class="form-select" required>
                                    <option value="0" {{ old('rodizio') == "0" ? 'selected' : '' }}>Não</option>
                                    <option value="1" {{ old('rodizio') == "1" ? 'selected' : '' }}>Sim</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dia_rodizio" class="form-label">Dia do Rodízio</label>
                                <input type="text" name="dia_rodizio" id="dia_rodizio" class="form-control"
                                    value="{{ old('dia_rodizio') }}" readonly placeholder="Será preenchido automaticamente">
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <a href="{{ route('veiculos.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
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
    const placaInput = document.getElementById('placa');

    // Máscara inteligente da placa
    placaInput.addEventListener('input', function () {
        let valor = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

        if (valor.length > 3) {
            valor = valor.slice(0, 3) + '-' + valor.slice(3);
        }

        this.value = valor.slice(0, 8);
    });

    // Rodízio automático
    document.getElementById('rodizio').addEventListener('change', function () {
        const rodizio = this.value;
        const placa = placaInput.value.replace('-', '');

        if (rodizio === '1' && placa.length === 7) {
            const digito = placa.slice(-1);
            let dia = '';

            if (['1', '2'].includes(digito)) dia = 'Segunda-feira';
            else if (['3', '4'].includes(digito)) dia = 'Terça-feira';
            else if (['5', '6'].includes(digito)) dia = 'Quarta-feira';
            else if (['7', '8'].includes(digito)) dia = 'Quinta-feira';
            else dia = 'Sexta-feira';

            document.getElementById('dia_rodizio').value = dia;
        } else {
            document.getElementById('dia_rodizio').value = '';
        }
    });

    // Limita entrada de ano a 4 dígitos
    ['ano_fabricacao', 'ano_modelo'].forEach(function(id) {
        const input = document.getElementById(id);
        input.addEventListener('input', function () {
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });
    });
});
</script>

@endpush
