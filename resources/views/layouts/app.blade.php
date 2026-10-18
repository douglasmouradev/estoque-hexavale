@php use Illuminate\Support\Facades\Auth; @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HexaVale Estoque')</title>
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body>
<div class="hx-app">
    <aside class="hx-side">
        <a href="{{ Auth::check() ? route('conta') : route('entrar') }}" class="hx-logo">Hexa<em>vale</em></a>
        <p class="hx-side__sub">Estoque · fazenda</p>

        <nav class="hx-side__nav" aria-label="Principal">
            @auth
                <a href="{{ route('painel') }}" @class(['on' => request()->routeIs('painel')])>Painel (protótipo)</a>
                <p class="hx-side__grupo">Estoque</p>
                <a href="{{ route('estoque.saldo') }}" @class(['on' => request()->routeIs('estoque.saldo')])>Saldo</a>
                <a href="{{ route('estoque.entrada') }}" @class(['on' => request()->routeIs('estoque.entrada*')])>Entrada</a>
                <a href="{{ route('estoque.saida') }}" @class(['on' => request()->routeIs('estoque.saida*')])>Saída</a>
                <p class="hx-side__grupo">Conta</p>
                <a href="{{ route('conta') }}" @class(['on' => request()->routeIs('conta', 'conta.*')])>Conta</a>
                <a href="{{ route('admin.usuarios') }}" @class(['on' => request()->routeIs('admin.usuarios*')])>Usuários</a>
                @if (app()->environment(['local', 'testing']))
                    <p class="hx-side__grupo">Desenvolvimento</p>
                    <a href="{{ route('styleguide') }}" @class(['on' => request()->routeIs('styleguide')])>Styleguide</a>
                @endif
                <form method="post" action="{{ route('sair') }}" class="hx-side__sair">
                    @csrf
                    <button type="submit" class="hx-btn hx-btn--ghost hx-btn--block hx-btn--sm">Sair</button>
                </form>
            @else
                <a href="{{ route('entrar') }}" @class(['on' => request()->routeIs('entrar*')])>Entrar</a>
                @if (app()->environment(['local', 'testing']))
                    <a href="{{ route('styleguide') }}" @class(['on' => request()->routeIs('styleguide')])>Styleguide</a>
                @endif
            @endauth
        </nav>

        @auth
            <p class="hx-side__nota">Sessão ativa. Dados sintéticos de demonstração.</p>
        @endauth
    </aside>

    <main class="hx-main">
        <header class="hx-page__cab">
            <div>
                @hasSection('breadcrumb')
                    <p class="hx-breadcrumb">@yield('breadcrumb')</p>
                @endif
                <h1>@yield('heading', 'Painel')</h1>
                @hasSection('lead')
                    <p class="hx-page__lead">@yield('lead')</p>
                @endif
            </div>
            <div class="hx-page__acoes noprint">
                @yield('acoes')
            </div>
        </header>

        @if (session('ok'))
            <p class="hx-alert hx-alert--ok" role="status">
                <svg class="hx-ico hx-ico--sm" aria-hidden="true"><use href="/icons.svg#i-check"/></svg>
                {{ session('ok') }}
            </p>
        @endif

        @yield('content')
    </main>

    @auth
        <nav class="hx-nav-bottom noprint" aria-label="Atalhos">
            <a href="{{ route('conta') }}" @class(['on' => request()->routeIs('conta*')])>Conta</a>
            <a href="{{ route('admin.usuarios') }}" @class(['on' => request()->routeIs('admin.usuarios*')])>Usuários</a>
            <a href="{{ route('styleguide') }}">Guia</a>
            <form method="post" action="{{ route('sair') }}" style="flex:1;margin:0;">
                @csrf
                <button type="submit" class="hx-btn hx-btn--ghost hx-btn--sm" style="width:100%;border:0;min-height:var(--touch-min);">Sair</button>
            </form>
        </nav>
    @endauth
</div>
</body>
</html>
