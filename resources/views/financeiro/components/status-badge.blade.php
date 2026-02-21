<span class="badge {{ $status == 'pendente' ? 'bg-warning' : ($status == 'recebido' ? 'bg-success' : ($status == 'pago' ? 'bg-success' : 'bg-secondary')) }}">
    {{ ucfirst($status) }}
</span>