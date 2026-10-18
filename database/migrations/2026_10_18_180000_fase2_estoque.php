<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id');
            $table->string('nome');
            $table->string('ingrediente_ativo')->nullable();
            $table->string('classe')->nullable();
            $table->string('unidade_base', 8);
            $table->decimal('minimo', 24, 6)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->index(['fazenda_id', 'nome']);
        });

        Schema::create('lotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id');
            $table->uuid('produto_id');
            $table->string('codigo');
            $table->date('validade')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->foreign('produto_id')->references('id')->on('produtos')->restrictOnDelete();
            $table->unique(['fazenda_id', 'produto_id', 'codigo']);
        });

        Schema::create('talhoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id');
            $table->string('codigo');
            $table->string('cultura')->nullable();
            $table->string('cultivar')->nullable();
            $table->decimal('area_ha', 12, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->unique(['fazenda_id', 'codigo']);
        });

        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id');
            $table->uuid('lote_id');
            $table->string('tipo', 32);
            $table->decimal('quantidade', 24, 6);
            $table->string('unidade', 8);
            $table->string('motivo')->nullable();
            $table->string('referencia_tipo')->nullable();
            $table->string('referencia_id')->nullable();
            $table->string('ator_id')->nullable();
            $table->string('idempotency_key', 64)->nullable();
            $table->timestamp('ocorreu_em', 3);
            $table->timestamps();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->foreign('lote_id')->references('id')->on('lotes')->restrictOnDelete();
            $table->unique(['fazenda_id', 'idempotency_key']);
            $table->index(['fazenda_id', 'lote_id', 'ocorreu_em']);
        });

        Schema::create('saldos_lote', function (Blueprint $table) {
            $table->uuid('lote_id')->primary();
            $table->uuid('fazenda_id');
            $table->decimal('fisico', 24, 6)->default(0);
            $table->decimal('reservado', 24, 6)->default(0);
            $table->timestamp('atualizado_em')->useCurrent();
            $table->foreign('lote_id')->references('id')->on('lotes')->cascadeOnDelete();
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::unprepared('CREATE TRIGGER movimentacoes_no_update BEFORE UPDATE ON movimentacoes FOR EACH ROW SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'movimentacoes é append-only\'');
            DB::unprepared('CREATE TRIGGER movimentacoes_no_delete BEFORE DELETE ON movimentacoes FOR EACH ROW SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'movimentacoes é append-only\'');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS movimentacoes_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS movimentacoes_no_delete');
        }
        Schema::dropIfExists('saldos_lote');
        Schema::dropIfExists('movimentacoes');
        Schema::dropIfExists('talhoes');
        Schema::dropIfExists('lotes');
        Schema::dropIfExists('produtos');
    }
};
