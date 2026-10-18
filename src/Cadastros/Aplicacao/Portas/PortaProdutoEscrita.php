<?php

declare(strict_types=1);

namespace HexaVale\Cadastros\Aplicacao\Portas;

interface PortaProdutoEscrita
{
    /**
     * @return array{ok: bool, mensagem?: string}
     */
    public function excluirSeNaoReferenciado(string $fazendaId, string $produtoId): array;
}
