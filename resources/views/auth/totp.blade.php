@extends('layouts.guest')

@section('title', 'MFA · HexaVale')
@section('heading', 'Verificação')
@section('lead', 'Informe o código de 6 dígitos do autenticador.')

@section('content')
<form method="post" action="{{ route('entrar.totp') }}" class="hx-form">
    @csrf
    <input type="hidden" name="user_id" value="{{ $userId }}">
    <label class="hx-field">
        Código
        <input type="text" name="codigo" inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
               required autocomplete="one-time-code" class="mono hx-field__otp">
    </label>
    @error('codigo')
        <p class="hx-field__erro">{{ $message }}</p>
    @enderror
    <button type="submit" class="hx-btn hx-btn--block">Confirmar</button>
</form>
@endsection
