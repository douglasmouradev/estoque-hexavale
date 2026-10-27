<?php

declare(strict_types=1);

namespace HexaVale\OperacaoAgricola\Dominio;

enum StatusOrdem: string
{
    case Rascunho = 'rascunho';
    case Liberada = 'liberada';
    case Baixada = 'baixada';
    case Cancelada = 'cancelada';
}
