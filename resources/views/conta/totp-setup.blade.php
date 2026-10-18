@extends('layouts.app')

@section('title', 'Ativar MFA · HexaVale')
@section('heading', 'Ativar TOTP')
@section('lead', 'Escaneie no autenticador ou digite o segredo manualmente.')

@section('content')
<section class="hx-card hx-card--narrow">
    <p class="hx-muted">URI otpauth</p>
    <pre class="hx-code">{{ $uri }}</pre>
    <p class="mono">Segredo: {{ $segredo }}</p>

    <form method="post" action="{{ route('conta.totp') }}" class="hx-form" style="margin-top:var(--hx-space-4);">
        @csrf
        <label class="hx-field">
            Confirme com um código
            <input type="text" name="codigo" maxlength="6" pattern="[0-9]{6}" required class="mono hx-field__otp">
        </label>
        @error('codigo')
            <p class="hx-field__erro">{{ $message }}</p>
        @enderror
        <button type="submit" class="hx-btn">Ativar MFA</button>
    </form>
</section>
@endsection
