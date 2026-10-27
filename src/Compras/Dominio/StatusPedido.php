<?php

declare(strict_types=1);

namespace HexaVale\Compras\Dominio;

enum StatusPedido: string
{
    case Rascunho = 'rascunho';
    case Solicitado = 'solicitado';
    case Aprovado = 'aprovado';
    case Rejeitado = 'rejeitado';
    case Recebido = 'recebido';
}
