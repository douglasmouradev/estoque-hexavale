<?php

declare(strict_types=1);

namespace HexaVale\Compartilhado\Dominio;

/**
 * Marcador de versão do núcleo de domínio (Fase 0).
 * Sem dependências de framework.
 */
final readonly class VersaoDominio
{
    public const string FASE = '0';

    public static function rotulo(): string
    {
        return 'HexaVale Dominio Fase '.self::FASE;
    }
}
