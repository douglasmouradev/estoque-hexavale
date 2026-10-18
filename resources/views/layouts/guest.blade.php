<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HexaVale Estoque')</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="hx-guest">
<div class="hx-guest__panel">
    <p class="hx-logo hx-logo--lg">Hexa<em>vale</em></p>
    <h1 class="hx-guest__title">@yield('heading', 'Entrar')</h1>
    @hasSection('lead')
        <p class="hx-guest__lead">@yield('lead')</p>
    @endif
    @yield('content')
</div>
</body>
</html>
