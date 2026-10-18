<?php

declare(strict_types=1);

namespace HexaVale\Identidade\Dominio;

enum Permissao: string
{
    case AdminUsuarios = 'admin.usuarios';
    case PiiRevelar = 'pii.revelar';
    case ContaPropria = 'conta.propria';
    case AuditoriaLer = 'auditoria.ler';
    case RopaGerir = 'ropa.gerir';
    case EstoqueLer = 'estoque.ler';
    case EstoqueMovimentar = 'estoque.movimentar';
    case CadastrosGerir = 'cadastros.gerir';
}
