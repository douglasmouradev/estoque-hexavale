<?php

declare(strict_types=1);

namespace HexaVale\Compras\Dominio;

use InvalidArgumentException;

/** RN-12 — solicitante ≠ aprovador. */
final class PoliticaAprovacao
{
    public static function garantirSegregacao(?string $solicitanteId, string $aprovadorId): void
    {
        if ($aprovadorId === '') {
            throw new InvalidArgumentException('Aprovador obrigatório (RN-12).');
        }
        if ($solicitanteId !== null && $solicitanteId !== '' && $solicitanteId === $aprovadorId) {
            throw new InvalidArgumentException('Solicitante não pode aprovar o próprio pedido (RN-12).');
        }
    }
}
