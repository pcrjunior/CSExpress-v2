@extends('layouts.app')

@section('content')
    <h1>Detalhes da Filial</h1>
    <p><strong>Nome:</strong> {{ $filial->nome }}</p>
    <p><strong>CNPJ:</strong> {{ $filial->cnpj }}</p>
    <!-- Exiba os demais campos conforme necessário -->

    <a href="{{ route('filiais.edit', $filial) }}">Editar</a>

    <form action="{{ route('filiais.destroy', $filial) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar esta filial?');">
        @csrf
        @method('DELETE')
        <button type="submit">Deletar</button>
    </form>
@endsection
