<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Expresso Oliveira Santos')</title>

    <!-- Bootstrap 5 -->

    <script src="{{ asset('js/jquery/3.7.1/jquery.min.js') }}"></script>
    <link href="{{ asset('css/bootstrap/5.3.3/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Select2 -->
    <link href="{{ asset('css/select2/4.0.13/dist/css/select2.min.css') }}" rel="stylesheet">

    
    <style>
        body {
            background-color: #fff;
            color: #000;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        @yield('content')
    </div>

    <!-- Select2 JS -->
    <script src="{{ asset('js/select2/4.0.13/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap/5.3.3/dist/js/bootstrap.bundle.min.js') }}"></script>    

    @stack('scripts')
</body>
</html>
