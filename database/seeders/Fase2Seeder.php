<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Fazenda;
use App\Models\Lote;
use App\Models\Produto;
use App\Models\Talhao;
use HexaVale\Compartilhado\Dominio\Quantidade;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Dominio\TipoMovimentacao;
use Illuminate\Database\Seeder;

/** BK-PROTO-01 — dados sintéticos do protótipo OT/estoque. */
final class Fase2Seeder extends Seeder
{
    public function run(): void
    {
        $fazenda = Fazenda::query()->first();
        if ($fazenda === null) {
            return;
        }
        $fazendaId = (string) $fazenda->id;

        /** @var PortaLedgerEstoque $ledger */
        $ledger = app(PortaLedgerEstoque::class);

        $itens = [
            ['nome' => 'Amistar WG', 'ia' => 'Azoxistrobina 500 g/kg', 'cls' => 'Fungicida', 'lote' => 'AM-DEMO-01', 'u' => 'kg', 'saldo' => '18.5', 'min' => '2'],
            ['nome' => 'Curygen EC', 'ia' => 'Difenoconazol 250 g/L', 'cls' => 'Fungicida', 'lote' => 'CG-DEMO-07', 'u' => 'L', 'saldo' => '42', 'min' => '5'],
            ['nome' => 'Fungicida X (sem bula)', 'ia' => '—', 'cls' => 'Fungicida', 'lote' => 'FX-DEMO-02', 'u' => 'L', 'saldo' => '10', 'min' => '1'],
            ['nome' => 'Nitrato de Cálcio', 'ia' => 'Fertilizante 15,5% N', 'cls' => 'Fertilizante', 'lote' => 'NC-DEMO-05', 'u' => 'kg', 'saldo' => '800', 'min' => '50'],
        ];

        foreach ($itens as $item) {
            $produtoId = UuidV7::gerar()->valor;
            Produto::query()->create([
                'id' => $produtoId,
                'fazenda_id' => $fazendaId,
                'nome' => $item['nome'],
                'ingrediente_ativo' => $item['ia'],
                'classe' => $item['cls'],
                'unidade_base' => $item['u'],
                'minimo' => $item['min'],
            ]);
            $loteId = UuidV7::gerar()->valor;
            Lote::query()->create([
                'id' => $loteId,
                'fazenda_id' => $fazendaId,
                'produto_id' => $produtoId,
                'codigo' => $item['lote'],
                'validade' => '2027-03-15',
            ]);
            $ledger->registrar(
                $fazendaId,
                $loteId,
                TipoMovimentacao::Entrada,
                Quantidade::de($item['saldo']),
                $item['u'],
                'Seed protótipo Fase 2',
                null,
                'seed-fase2-'.$item['lote'],
            );
        }

        Talhao::query()->create([
            'id' => UuidV7::gerar()->valor,
            'fazenda_id' => $fazendaId,
            'codigo' => 'N 03',
            'cultura' => 'Manga',
            'cultivar' => 'Kent',
            'area_ha' => '8.3000',
        ]);
        Talhao::query()->create([
            'id' => UuidV7::gerar()->valor,
            'fazenda_id' => $fazendaId,
            'codigo' => 'N 04',
            'cultura' => 'Manga',
            'cultivar' => 'Kent',
            'area_ha' => '6.2000',
        ]);
    }
}
