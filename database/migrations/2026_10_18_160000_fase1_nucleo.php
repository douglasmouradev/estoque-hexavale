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
        Schema::create('fazendas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->text('cnpj_cifrado')->nullable();
            $table->string('timezone')->default('America/Recife');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->text('telefone_cifrado')->nullable();
            $table->string('telefone_blind', 64)->nullable()->index();
            $table->text('cpf_cifrado')->nullable();
            $table->string('cpf_blind', 64)->nullable()->unique();
            $table->text('mfa_secret_cifrado')->nullable();
            $table->boolean('mfa_enabled')->default(false);
            $table->softDeletes();
        });

        Schema::create('papeis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo')->unique();
            $table->string('nome');
            $table->timestamps();
        });

        Schema::create('permissoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo')->unique();
            $table->string('descricao');
            $table->timestamps();
        });

        Schema::create('papel_permissao', function (Blueprint $table) {
            $table->uuid('papel_id');
            $table->uuid('permissao_id');
            $table->primary(['papel_id', 'permissao_id']);
            $table->foreign('papel_id')->references('id')->on('papeis')->cascadeOnDelete();
            $table->foreign('permissao_id')->references('id')->on('permissoes')->cascadeOnDelete();
        });

        Schema::create('usuario_papel_fazenda', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('fazenda_id');
            $table->uuid('papel_id');
            $table->timestamps();
            $table->unique(['user_id', 'fazenda_id', 'papel_id']);
            $table->foreign('fazenda_id')->references('id')->on('fazendas')->cascadeOnDelete();
            $table->foreign('papel_id')->references('id')->on('papeis')->cascadeOnDelete();
        });

        Schema::create('dispositivos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nome');
            $table->string('user_agent')->nullable();
            $table->timestamp('ultimo_acesso_em')->nullable();
            $table->timestamp('revogado_em')->nullable();
            $table->timestamps();
        });

        Schema::create('credenciais_passkey', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('credential_id')->unique();
            $table->text('public_key');
            $table->unsignedBigInteger('sign_count')->default(0);
            $table->string('nome')->nullable();
            $table->timestamps();
        });

        Schema::create('auditoria', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('fazenda_id')->nullable()->index();
            $table->string('ator_id')->nullable()->index();
            $table->string('acao');
            $table->string('entidade');
            $table->string('entidade_id');
            $table->json('antes')->nullable();
            $table->json('depois')->nullable();
            $table->string('motivo')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('ocorreu_em', 3);
            $table->char('hash_anterior', 64);
            $table->char('hash', 64)->unique();
        });

        Schema::create('ropa_tratamentos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome');
            $table->string('finalidade');
            $table->string('base_legal');
            $table->string('categorias_dados');
            $table->string('titulares');
            $table->string('retencao');
            $table->string('compartilhamento')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('acessos_pii', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('ator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('titular_id')->constrained('users')->cascadeOnDelete();
            $table->string('campo');
            $table->string('motivo');
            $table->uuid('fazenda_id')->nullable();
            $table->timestamp('acessado_em');
            $table->timestamps();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::unprepared('CREATE TRIGGER auditoria_no_update BEFORE UPDATE ON auditoria BEGIN SELECT RAISE(ABORT, "auditoria imutavel"); END;');
            DB::unprepared('CREATE TRIGGER auditoria_no_delete BEFORE DELETE ON auditoria BEGIN SELECT RAISE(ABORT, "auditoria imutavel"); END;');
        } elseif ($driver === 'mysql') {
            DB::unprepared('CREATE TRIGGER auditoria_no_update BEFORE UPDATE ON auditoria FOR EACH ROW SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "auditoria imutavel"');
            DB::unprepared('CREATE TRIGGER auditoria_no_delete BEFORE DELETE ON auditoria FOR EACH ROW SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "auditoria imutavel"');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite' || $driver === 'mysql') {
            DB::unprepared('DROP TRIGGER IF EXISTS auditoria_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS auditoria_no_delete');
        }
        Schema::dropIfExists('acessos_pii');
        Schema::dropIfExists('ropa_tratamentos');
        Schema::dropIfExists('auditoria');
        Schema::dropIfExists('credenciais_passkey');
        Schema::dropIfExists('dispositivos');
        Schema::dropIfExists('usuario_papel_fazenda');
        Schema::dropIfExists('papel_permissao');
        Schema::dropIfExists('permissoes');
        Schema::dropIfExists('papeis');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'uuid', 'telefone_cifrado', 'telefone_blind', 'cpf_cifrado', 'cpf_blind',
                'mfa_secret_cifrado', 'mfa_enabled', 'deleted_at',
            ]);
        });
        Schema::dropIfExists('fazendas');
    }
};
