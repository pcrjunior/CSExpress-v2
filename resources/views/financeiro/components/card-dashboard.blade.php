<!-- resources/views/financeiro/components/card-dashboard.blade.php -->
<div class="card">
    <div class="card-header bg-header-blue">
        <i class="fas fa-{{ $icon ?? 'chart-bar' }} me-2"></i> {{ $title ?? 'Dashboard' }}
    </div>
    <div class="card-body">
        {{ $slot }}
    </div>
</div>