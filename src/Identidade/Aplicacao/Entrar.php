<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Aplicacao;

use HexaVale\Compartilhado\Dominio\Email;
use HexaVale\Identidade\Aplicacao\Portas\PortaAutenticacao;
use HexaVale\Identidade\Aplicacao\Portas\PortaUsuarioConsulta;

final readonly class Entrar
{
    public function __construct(
        private PortaUsuarioConsulta $usuarios,
        private PortaAutenticacao $auth,
    ) {}

    /**
     * @return array{ok: bool, precisa_totp?: bool, user_id?: string, mensagem?: string}
     */
    public function executar(string $email, string $senha): array
    {
        $emailVo = Email::de($email);
        $usuario = $this->usuarios->buscarPorEmail($emailVo);
        if ($usuario === null || ! $this->auth->senhaConfere($usuario['password'], $senha)) {
            return ['ok' => false, 'mensagem' => 'Credenciais inválidas.'];
        }
        if ($usuario['mfa_enabled']) {
            $this->auth->guardarDesafioMfa($usuario['id']);

            return ['ok' => true, 'precisa_totp' => true, 'user_id' => $usuario['id']];
        }
        $this->auth->iniciarSessao($usuario['id']);

        return ['ok' => true, 'precisa_totp' => false, 'user_id' => $usuario['id']];
    }
}
