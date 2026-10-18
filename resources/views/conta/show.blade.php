@extends('layouts.app')

@section('title', 'Conta · HexaVale')
@section('heading', 'Minha conta')
@section('lead', 'Identidade, MFA e contexto da fazenda.')

@section('content')
<div class="hx-grid hx-grid--2">
    <section class="hx-card">
        <h2>Perfil</h2>
        <dl class="hx-dl">
            <div>
                <dt>Nome</dt>
                <dd>{{ $user->name }}</dd>
            </div>
            <div>
                <dt>E-mail</dt>
                <dd class="mono">{{ $user->email }}</dd>
            </div>
            <div>
                <dt>Fazenda ativa</dt>
                <dd class="mono">{{ $fazendaId ?? '—' }}</dd>
            </div>
        </dl>
    </section>

    <section class="hx-card">
        <h2>Autenticação</h2>
        <p class="hx-row">
            MFA TOTP
            @if ($user->mfa_enabled)
                <span class="hx-tag">ativo</span>
            @else
                <span class="hx-tag hx-tag--wa">inativo</span>
                <a href="{{ route('conta.totp') }}" class="hx-btn hx-btn--ghost hx-btn--sm">Ativar</a>
            @endif
        </p>
        <p class="hx-muted">Passkeys: tabela pronta; cerimônia WebAuthn em BK-PASSKEY-01.</p>
    </section>
</div>
@endsection
