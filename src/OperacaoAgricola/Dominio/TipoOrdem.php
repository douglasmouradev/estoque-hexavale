<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Dominio;

enum TipoOrdem: string
{
    case Pulverizacao = 'pulverizacao';
    case Adubacao = 'adubacao';
}
