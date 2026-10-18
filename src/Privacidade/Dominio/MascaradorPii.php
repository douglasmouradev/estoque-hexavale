<?php

declare(strict_types=1);

namespace HexaVale\Privacidade\Dominio;

use HexaVale\Compartilhado\Dominio\Cpf;

final class MascaradorPii
{
    public function cpf(string $digitosOuFormatado): string
    {
        $digitos = preg_replace('/\D+/', '', $digitosOuFormatado) ?? '';
        if (strlen($digitos) !== 11) {
            return '***.***.***-**';
        }

        return '***.'.substr($digitos, 3, 3).'.'.substr($digitos, 6, 3).'-**';
    }

    public function cpfDeVo(Cpf $cpf): string
    {
        return $this->cpf($cpf->digitos);
    }

    public function email(string $email): string
    {
        $partes = explode('@', $email, 2);
        if (count($partes) !== 2) {
            return '***@***';
        }
        $local = $partes[0];
        $visivel = mb_substr($local, 0, 1);

        return $visivel.'***@'.$partes[1];
    }

    public function telefone(string $telefone): string
    {
        $digitos = preg_replace('/\D+/', '', $telefone) ?? '';
        if (strlen($digitos) < 8) {
            return '(**) *****-****';
        }

        return '(**) *****-'.substr($digitos, -4);
    }
}
