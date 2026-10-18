<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Dominio;

enum PapelCodigo: string
{
    case OperadorCampo = 'operador_campo';
    case Almoxarife = 'almoxarife';
    case Comprador = 'comprador';
    case Aprovador = 'aprovador';
    case ResponsavelTecnico = 'responsavel_tecnico';
    case Gestor = 'gestor';
    case Mecanico = 'mecanico';
    case Auditor = 'auditor';
    case Dpo = 'dpo';
    case AdminTi = 'admin_ti';
}
