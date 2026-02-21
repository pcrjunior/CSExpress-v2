<div class="card {{ $bgClass }} {{ $textClass ?? '' }} mb-3">
    <div class="card-body">
        <h5 class="card-title">{!! $title !!}</h5>
        <h3>R$ {{ number_format($valor, 2, ',', '.') }}</h3>
        <small>{{ $descricao }}</small>
    </div>
</div>