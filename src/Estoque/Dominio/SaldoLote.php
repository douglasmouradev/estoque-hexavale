<?php

declare(strict_types=1);

namespace HexaVale\Estoque\Dominio;

use HexaVale\Compartilhado\Dominio\Quantidade;
use InvalidArgumentException;

/** RN-02: disponível = físico − reservado */
final readonly class SaldoLote
{
    public function __construct(
        public Quantidade $fisico,
        public Quantidade $reservado,
    ) {
        if ($this->fisico->isNegativa() || $this->reservado->isNegativa()) {
            throw new InvalidArgumentException('Saldo não pode ser negativo.');
        }
        if ($this->reservado->maiorQue($this->fisico)) {
            throw new InvalidArgumentException('Reservado não pode exceder o físico.');
        }
    }

    public function disponivel(): Quantidade
    {
        return $this->fisico->subtrair($this->reservado);
    }

    public function aplicarEntrada(Quantidade $q): self
    {
        return new self($this->fisico->somar($q), $this->reservado);
    }

    public function aplicarSaida(Quantidade $q): self
    {
        if ($q->maiorQue($this->disponivel())) {
            throw new InvalidArgumentException('Saída sem saldo disponível suficiente (RN-07).');
        }

        return new self($this->fisico->subtrair($q), $this->reservado);
    }

    public function aplicarReserva(Quantidade $q): self
    {
        if ($q->maiorQue($this->disponivel())) {
            throw new InvalidArgumentException('Reserva sem saldo disponível suficiente.');
        }

        return new self($this->fisico, $this->reservado->somar($q));
    }

    public function liberarReserva(Quantidade $q): self
    {
        if ($q->maiorQue($this->reservado)) {
            throw new InvalidArgumentException('Liberação excede o reservado.');
        }

        return new self($this->fisico, $this->reservado->subtrair($q));
    }

    /**
     * RN-09 — contagem física ≥ reservado; retorna novo saldo (físico = contagem).
     */
    public function ajustarParaContagem(Quantidade $contagem): self
    {
        if ($contagem->isNegativa()) {
            throw new InvalidArgumentException('Contagem não pode ser negativa.');
        }
        if ($contagem->menorQue($this->reservado)) {
            throw new InvalidArgumentException('Contagem menor que o saldo reservado (RN-09).');
        }

        return new self($contagem, $this->reservado);
    }

    public function situacao(Quantidade $minimo): SituacaoSaldo
    {
        return SituacaoSaldo::de($this->disponivel(), $minimo);
    }
}
