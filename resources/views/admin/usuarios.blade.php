@extends('layouts.app')

@section('title', 'Usuários · HexaVale')
@section('heading', 'Usuários')
@section('lead', 'RBAC por fazenda. Revelação de CPF é auditada.')

@section('content')
@if (session('pii_revelado'))
    <p class="hx-alert hx-alert--wa" role="status">
        CPF revelado (usuário {{ session('pii_revelado')['user_id'] }}):
        <span class="mono">{{ session('pii_revelado')['cpf'] }}</span>
    </p>
@endif

<section class="hx-card">
    <div class="hx-table-wrap">
        <table class="hx-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>CPF</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $u)
                    <tr>
                        <td>{{ $u['name'] }}</td>
                        <td class="mono">{{ $u['email'] }}</td>
                        <td class="mono">{{ $u['cpf'] }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.usuarios.revelar', $u['id']) }}" class="hx-inline-form">
                                @csrf
                                <input type="text" name="motivo" placeholder="Motivo" required aria-label="Motivo da revelação">
                                <button type="submit" class="hx-btn hx-btn--sm">Revelar CPF</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
