<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Expresso Oliveira Santos')</title>

    <link href="{{ asset('css/bootstrap/5.3.3/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap/1.11.3/font/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/select2/4.0.13/dist/css/select2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/6.4.0/css/all.min.css') }}">


    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">


    <!-- Estilos globais -->
    <style>
        body {
            font-size: 0.875rem; /* Fonte base menor */
        }

        .btn-xs {
            padding: 0.2rem 0.4rem;
            font-size: 0.72rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }

        .shadow-xs {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .05) !important;
        }

        h7 {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .table th, .table td {
            vertical-align: middle !important;
        }
    </style>

    <!-- Estilos adicionais -->
    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d47a1;">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Expresso Oliveira Santos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Menu principal à esquerda -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>

                        <li class="nav-item dropdown">
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <x-gerenciamento-menu />
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('ordemservicos.index') }}">Ordem de Serviço</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('financeiro.index') }}">Financeiro</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="relatorioDropdown" role="button" data-bs-toggle="dropdown">
                                Relatórios
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="relatorioDropdown">
                                <li><a class="dropdown-item" href="{{ route('relatorios.ordens-servico') }}">Ordem de Serviço</a></li>
                                <li><a class="dropdown-item" href="{{ route('relatorios.clientes-atendidos') }}">Clientes</a></li>
                                <!-- <li><a class="dropdown-item" href="{{ route('relatorios.entregadores') }}">Entregadores</a></li> -->
                                <li><a class="dropdown-item" href="{{ route('relatorios.motoristas') }}">Motoristas</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header">Financeiro</li>
                                <li><a class="dropdown-item" href="{{ route('relatorios.contas-pagar') }}">Contas a Pagar</a></li>
                                <li><a class="dropdown-item" href="{{ route('relatorios.contas-receber') }}">Contas a Receber</a></li>
                            </ul>
                        </li>

                        <!-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('consulta') }}">Consulta</a>
                        </li> -->
                    @endauth
                </ul>

                <!-- Menu de perfil à direita -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Sair</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo principal -->
    <main class="container-fluid my-2">
        @yield('content')
    </main>

    <!-- Scripts JS -->
    <!-- <script src="{{ asset('js/jquery/mask/3.7.1/dist/jquery.min.js') }}"></script>   -->
    <script src="{{ asset('js/jquery/3.7.1/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery/mask/1.14.16/jquery/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('js/select2/4.0.13/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/5.3.3/dist/js/bootstrap.bundle.min.js') }}"></script>
  

    @stack('scripts')
</body>
</html>
