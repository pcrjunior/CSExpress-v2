<div class="card mb-4">
    <div class="card-header bg-header-blue text-white">
        <i class="fas fa-{{ $icon ?? 'info-circle' }} me-2"></i> {{ $title }}
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>

