<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Dominio;

use DateTimeImmutable;
use InvalidArgumentException;

/** RN-03 — lote vencido bloqueia uso (saída/reserva/ajuste negativo). */
final class PoliticaLote
{
    public static function garantirUsoPermitido(?string $validadeYmd, ?DateTimeImmutable $hoje = null): void
    {
        if ($validadeYmd === null || $validadeYmd === '') {
            return;
        }
        $hoje ??= new DateTimeImmutable('today');
        $validade = DateTimeImmutable::createFromFormat('!Y-m-d', substr($validadeYmd, 0, 10));
        if ($validade === false) {
            throw new InvalidArgumentException('Validade do lote inválida.');
        }
        if ($validade < $hoje) {
            throw new InvalidArgumentException('Lote vencido: uso bloqueado (RN-03).');
        }
    }

    public static function estaVencido(?string $validadeYmd, ?DateTimeImmutable $hoje = null): bool
    {
        if ($validadeYmd === null || $validadeYmd === '') {
            return false;
        }
        try {
            self::garantirUsoPermitido($validadeYmd, $hoje);

            return false;
        } catch (InvalidArgumentException) {
            return true;
        }
    }
}
