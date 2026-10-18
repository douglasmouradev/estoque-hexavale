@extends('layouts.guest')

@section('title', 'Entrar · HexaVale')
@section('heading', 'Entrar')
@section('lead', 'Acesso ao estoque e operação da fazenda.')

@section('content')
<form method="post" action="{{ route('entrar.auth') }}" class="hx-form">
    @csrf
    <label class="hx-field">
        E-mail
        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
    </label>
    <label class="hx-field">
        Senha
        <input type="password" name="password" required autocomplete="current-password">
    </label>
    @error('email')
        <p class="hx-field__erro">{{ $message }}</p>
    @enderror
    <button type="submit" class="hx-btn hx-btn--block">Entrar</button>
    <p class="hx-demo">Demo: admin@hexavale.local / HexaVale!Admin1</p>
</form>
@endsection
