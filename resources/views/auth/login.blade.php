@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="card p-4 shadow login-card" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <img src="{{ asset('images/ecs-logo.png') }}" alt="Logo" width="70" height="70">
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input id="email" type="email" class="form-control" name="email" required autocomplete="email" autofocus>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="password" class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">
                </div>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <a href="{{ route('password.request') }}" class="small text-decoration-none">Esqueceu sua senha?</a>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary fw-bold">
                    Entrar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
