<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Dominio;

use HexaVale\Compartilhado\Dominio\Quantidade;

/** RN-14 */
enum SituacaoSaldo: string
{
    case Zerado = 'zerado';
    case Abaixo = 'abaixo';
    case Ok = 'ok';

    public static function de(Quantidade $disponivel, Quantidade $minimo): self
    {
        if ($disponivel->isZero() || $disponivel->isNegativa()) {
            return self::Zerado;
        }
        if ($disponivel->menorQue($minimo)) {
            return self::Abaixo;
        }

        return self::Ok;
    }
}
