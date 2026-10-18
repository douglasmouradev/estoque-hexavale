<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Dominio;

enum TipoMovimentacao: string
{
    case Entrada = 'entrada';
    case Saida = 'saida';
    case AjustePos = 'ajuste_pos';
    case AjusteNeg = 'ajuste_neg';
    case Reserva = 'reserva';
    case BaixaReserva = 'baixa_reserva';

    public function afetaFisico(): string
    {
        return match ($this) {
            self::Entrada, self::AjustePos => '+',
            self::Saida, self::AjusteNeg => '-',
            self::Reserva, self::BaixaReserva => '0',
        };
    }

    public function afetaReservado(): string
    {
        return match ($this) {
            self::Reserva => '+',
            self::BaixaReserva => '-',
            default => '0',
        };
    }
}
