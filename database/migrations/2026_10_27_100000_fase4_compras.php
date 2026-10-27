<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos_compra', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id');
            $table->string('status', 32);
            $table->string('fornecedor_nome')->nullable();
            $table->string('observacao')->nullable();
            $table->string('solicitante_id');
            $table->string('aprovador_id')->nullable();
            $table->string('motivo_rejeicao')->nullable();
            $table->string('chave_nfe', 44)->nullable();
            $table->timestamp('solicitado_em')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamp('recebido_em')->nullable();
            $table->timestamps();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->index(['fazenda_id', 'status']);
            $table->unique(['fazenda_id', 'chave_nfe']);
        });

        Schema::create('pedido_compra_itens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pedido_id');
            $table->uuid('produto_id');
            $table->decimal('quantidade', 24, 6);
            $table->string('unidade', 8);
            $table->timestamps();
            $table->foreign('pedido_id')->references('id')->on('pedidos_compra')->cascadeOnDelete();
            $table->foreign('produto_id')->references('id')->on('produtos')->restrictOnDelete();
            $table->index(['pedido_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_compra_itens');
        Schema::dropIfExists('pedidos_compra');
    }
};
