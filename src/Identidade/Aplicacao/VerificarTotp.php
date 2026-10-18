<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Aplicacao;

use HexaVale\Identidade\Aplicacao\Portas\PortaAutenticacao;
use HexaVale\Identidade\Aplicacao\Portas\PortaTotp;
use HexaVale\Identidade\Aplicacao\Portas\PortaUsuarioConsulta;

final readonly class VerificarTotp
{
    public function __construct(
        private PortaUsuarioConsulta $usuarios,
        private PortaTotp $totp,
        private PortaAutenticacao $auth,
    ) {}

    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function executar(string $userId, string $codigo): array
    {
        $desafio = $this->auth->desafioMfaAtual();
        if ($desafio === null || $desafio !== $userId) {
            return ['ok' => false, 'mensagem' => 'Desafio MFA ausente ou expirado.'];
        }
        $usuario = $this->usuarios->buscarPorId($userId);
        if ($usuario === null) {
            return ['ok' => false, 'mensagem' => 'MFA não configurado.'];
        }
        $segredo = $usuario['mfa_secret'];
        if ($segredo === null || $segredo === '') {
            return ['ok' => false, 'mensagem' => 'MFA não configurado.'];
        }
        if (! $this->totp->verificar($segredo, $codigo)) {
            return ['ok' => false, 'mensagem' => 'Código TOTP inválido.'];
        }
        $this->auth->limparDesafioMfa();
        $this->auth->iniciarSessao($userId);

        return ['ok' => true];
    }
}
