<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Dominio;

use HexaVale\Compartilhado\Dominio\Quantidade;
use InvalidArgumentException;

/** RN-06 — área tratada ∈ (0, planejada]. */
final class PoliticaAreaTratada
{
    public static function garantir(string $areaTratada, string $areaPlanejada): Quantidade
    {
        $tratada = Quantidade::de($areaTratada);
        $planejada = Quantidade::de($areaPlanejada);
        if (! $tratada->maiorQue(Quantidade::zero())) {
            throw new InvalidArgumentException('Área tratada deve ser > 0 (RN-06).');
        }
        if ($tratada->maiorQue($planejada)) {
            throw new InvalidArgumentException('Área tratada não pode exceder a planejada (RN-06).');
        }

        return $tratada;
    }
}
