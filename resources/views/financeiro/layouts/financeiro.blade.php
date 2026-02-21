<!-- resources/views/financeiro/layouts/financeiro.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-{{ $icon ?? 'money-bill' }} me-2"></i> {{ $pageTitle ?? 'Financeiro' }}</h1>
        
        @if(isset($actions))
            <div>
                {{ $actions }}
            </div>
        @endif
    </div>
    
    @yield('financeiro-content')
</div>



@push('scripts')
    {{ $scripts ?? '' }}
@endpush
@endsection