<?php

declare(strict_types=1);

namespace HexaVale\Cadastros\Infraestrutura;

use App\Models\Lote;
use App\Models\Movimentacao;
use App\Models\Produto;
use HexaVale\Cadastros\Aplicacao\Portas\PortaProdutoEscrita;

final class EloquentProdutoEscrita implements PortaProdutoEscrita
{
    public function excluirSeNaoReferenciado(string $fazendaId, string $produtoId): array
    {
        $produto = Produto::query()->where('fazenda_id', $fazendaId)->where('id', $produtoId)->first();
        if ($produto === null) {
            return ['ok' => false, 'mensagem' => 'Produto não encontrado.'];
        }

        $loteIds = Lote::query()->where('produto_id', $produtoId)->pluck('id');
        if ($loteIds->isNotEmpty() && Movimentacao::query()->whereIn('lote_id', $loteIds)->exists()) {
            return ['ok' => false, 'mensagem' => 'Produto referenciado em movimentações; exclusão bloqueada (RN-13).'];
        }

        $produto->delete();

        return ['ok' => true];
    }
}
