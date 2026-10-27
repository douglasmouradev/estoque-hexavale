<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordens_agricolas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id');
            $table->uuid('talhao_id');
            $table->string('tipo', 32);
            $table->string('status', 32);
            $table->decimal('area_planejada_ha', 12, 4);
            $table->decimal('area_tratada_ha', 12, 4)->nullable();
            $table->decimal('calda_l_ha', 12, 4)->nullable();
            $table->string('observacao')->nullable();
            $table->string('motivo_cancelamento')->nullable();
            $table->string('criador_id');
            $table->timestamp('liberada_em')->nullable();
            $table->timestamp('baixada_em')->nullable();
            $table->timestamp('cancelada_em')->nullable();
            $table->timestamps();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->foreign('talhao_id')->references('id')->on('talhoes')->restrictOnDelete();
            $table->index(['fazenda_id', 'status']);
        });

        Schema::create('ordem_agricola_itens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ordem_id');
            $table->uuid('lote_id');
            $table->uuid('produto_id');
            $table->decimal('dose', 24, 6);
            $table->decimal('quantidade', 24, 6);
            $table->string('unidade', 8);
            $table->timestamps();
            $table->foreign('ordem_id')->references('id')->on('ordens_agricolas')->cascadeOnDelete();
            $table->foreign('lote_id')->references('id')->on('lotes')->restrictOnDelete();
            $table->foreign('produto_id')->references('id')->on('produtos')->restrictOnDelete();
            $table->index(['ordem_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordem_agricola_itens');
        Schema::dropIfExists('ordens_agricolas');
    }
};
