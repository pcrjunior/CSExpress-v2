<form action="{{ $route }}" method="GET" class="row g-3">
    {{ $slot }}

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="fas fa-search me-1"></i> Pesquisar
        </button>
        <a href="{{ $clearRoute }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-eraser me-1"></i> Limpar
        </a>
    </div>
</form>
