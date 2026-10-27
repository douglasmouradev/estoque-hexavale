<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Dominio;

use HexaVale\Compartilhado\Dominio\Quantidade;
use InvalidArgumentException;

/** RN-05 — quantidade = dose × área; pulverização pode usar calda L/ha. */
final class FormulaDose
{
    /**
     * Adubação / pulverização por ha: dose_por_ha × area_ha.
     * Pulverização com calda: (dose_por_100L / 100) × calda_L_ha × area_ha.
     */
    public static function calcular(
        TipoOrdem $tipo,
        string $dose,
        string $areaHa,
        ?string $caldaLHa = null,
    ): Quantidade {
        $area = Quantidade::de($areaHa);
        if (! $area->maiorQue(Quantidade::zero())) {
            throw new InvalidArgumentException('Área deve ser positiva (RN-05).');
        }
        $d = Quantidade::de($dose);
        if (! $d->maiorQue(Quantidade::zero())) {
            throw new InvalidArgumentException('Dose deve ser positiva (RN-05).');
        }

        if ($tipo === TipoOrdem::Pulverizacao && $caldaLHa !== null && $caldaLHa !== '') {
            $calda = Quantidade::de($caldaLHa);
            if (! $calda->maiorQue(Quantidade::zero())) {
                throw new InvalidArgumentException('Calda L/ha deve ser positiva (RN-05).');
            }

            // dose informada por 100 L de calda
            return $d->multiplicar($calda->paraString())->multiplicar($area->paraString())->multiplicar('0.01');
        }

        return $d->multiplicar($area->paraString());
    }
}
