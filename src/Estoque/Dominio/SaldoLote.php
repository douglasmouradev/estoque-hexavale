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

    public function situacao(Quantidade $minimo): SituacaoSaldo
    {
        return SituacaoSaldo::de($this->disponivel(), $minimo);
    }
}
