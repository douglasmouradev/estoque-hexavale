<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Fazenda;
use App\Models\Papel;
use App\Models\Permissao;
use App\Models\User;
use App\Models\UsuarioPapelFazenda;
use HexaVale\Compartilhado\Dominio\UuidV7;
use HexaVale\Identidade\Dominio\PapelCodigo;
use HexaVale\Identidade\Dominio\Permissao as PermissaoDominio;
use HexaVale\Privacidade\Dominio\CifradorCampo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class Fase1Seeder extends Seeder
{
    public function run(): void
    {
        /** @var CifradorCampo $cifrador */
        $cifrador = app(CifradorCampo::class);

        $fazendaId = UuidV7::gerar()->valor;
        Fazenda::query()->create([
            'id' => $fazendaId,
            'nome' => 'Hexavale Demo Petrolina',
            'timezone' => 'America/Recife',
        ]);

        $mapaPerm = [];
        foreach (PermissaoDominio::cases() as $p) {
            $id = UuidV7::gerar()->valor;
            Permissao::query()->create([
                'id' => $id,
                'codigo' => $p->value,
                'descricao' => $p->name,
            ]);
            $mapaPerm[$p->value] = $id;
        }

        $mapaPapel = [];
        foreach (PapelCodigo::cases() as $papel) {
            $id = UuidV7::gerar()->valor;
            Papel::query()->create([
                'id' => $id,
                'codigo' => $papel->value,
                'nome' => str_replace('_', ' ', $papel->value),
            ]);
            $mapaPapel[$papel->value] = $id;
        }

        $matriz = [
            PapelCodigo::AdminTi->value => array_keys($mapaPerm),
            PapelCodigo::Gestor->value => [
                PermissaoDominio::AdminUsuarios->value,
                PermissaoDominio::PiiRevelar->value,
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::AuditoriaLer->value,
                PermissaoDominio::EstoqueLer->value,
                PermissaoDominio::EstoqueMovimentar->value,
                PermissaoDominio::CadastrosGerir->value,
                PermissaoDominio::ComprasLer->value,
                PermissaoDominio::ComprasSolicitar->value,
                PermissaoDominio::ComprasAprovar->value,
                PermissaoDominio::ComprasReceber->value,
                PermissaoDominio::OperacaoLer->value,
                PermissaoDominio::OperacaoCriar->value,
                PermissaoDominio::OperacaoLiberar->value,
                PermissaoDominio::OperacaoBaixar->value,
            ],
            PapelCodigo::ResponsavelTecnico->value => [
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::EstoqueLer->value,
                PermissaoDominio::OperacaoLer->value,
                PermissaoDominio::OperacaoCriar->value,
                PermissaoDominio::OperacaoLiberar->value,
            ],
            PapelCodigo::Comprador->value => [
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::EstoqueLer->value,
                PermissaoDominio::ComprasLer->value,
                PermissaoDominio::ComprasSolicitar->value,
            ],
            PapelCodigo::Aprovador->value => [
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::EstoqueLer->value,
                PermissaoDominio::ComprasLer->value,
                PermissaoDominio::ComprasAprovar->value,
            ],
            PapelCodigo::Almoxarife->value => [
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::EstoqueLer->value,
                PermissaoDominio::EstoqueMovimentar->value,
                PermissaoDominio::CadastrosGerir->value,
                PermissaoDominio::ComprasLer->value,
                PermissaoDominio::ComprasReceber->value,
                PermissaoDominio::OperacaoLer->value,
                PermissaoDominio::OperacaoBaixar->value,
            ],
            PapelCodigo::Dpo->value => [
                PermissaoDominio::RopaGerir->value,
                PermissaoDominio::PiiRevelar->value,
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::AuditoriaLer->value,
            ],
            PapelCodigo::Auditor->value => [
                PermissaoDominio::AuditoriaLer->value,
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::EstoqueLer->value,
            ],
            PapelCodigo::OperadorCampo->value => [
                PermissaoDominio::ContaPropria->value,
                PermissaoDominio::EstoqueLer->value,
                PermissaoDominio::OperacaoLer->value,
                PermissaoDominio::OperacaoCriar->value,
                PermissaoDominio::OperacaoBaixar->value,
            ],
        ];

        foreach ($matriz as $papelCodigo => $perms) {
            foreach ($perms as $permCodigo) {
                DB::table('papel_permissao')->insert([
                    'papel_id' => $mapaPapel[$papelCodigo],
                    'permissao_id' => $mapaPerm[$permCodigo],
                ]);
            }
        }

        $admin = User::query()->create([
            'uuid' => UuidV7::gerar()->valor,
            'name' => 'Admin Demo',
            'email' => 'admin@hexavale.local',
            'password' => Hash::make('HexaVale!Admin1'),
            'cpf_cifrado' => $cifrador->cifrar('52998224725'),
            'cpf_blind' => $cifrador->indiceCego('52998224725'),
            'telefone_cifrado' => $cifrador->cifrar('87999990000'),
            'telefone_blind' => $cifrador->indiceCego('87999990000'),
            'mfa_enabled' => false,
        ]);

        UsuarioPapelFazenda::query()->create([
            'id' => UuidV7::gerar()->valor,
            'user_id' => $admin->id,
            'fazenda_id' => $fazendaId,
            'papel_id' => $mapaPapel[PapelCodigo::AdminTi->value],
        ]);

        $operador = User::query()->create([
            'uuid' => UuidV7::gerar()->valor,
            'name' => 'Operador Demo',
            'email' => 'operador@hexavale.local',
            'password' => Hash::make('HexaVale!Op1'),
            'cpf_cifrado' => $cifrador->cifrar('39053344705'),
            'cpf_blind' => $cifrador->indiceCego('39053344705'),
            'mfa_enabled' => false,
        ]);

        UsuarioPapelFazenda::query()->create([
            'id' => UuidV7::gerar()->valor,
            'user_id' => $operador->id,
            'fazenda_id' => $fazendaId,
            'papel_id' => $mapaPapel[PapelCodigo::OperadorCampo->value],
        ]);

        $comprador = User::query()->create([
            'uuid' => UuidV7::gerar()->valor,
            'name' => 'Comprador Demo',
            'email' => 'comprador@hexavale.local',
            'password' => Hash::make('HexaVale!Comp1'),
            'cpf_cifrado' => $cifrador->cifrar('15350946056'),
            'cpf_blind' => $cifrador->indiceCego('15350946056'),
            'mfa_enabled' => false,
        ]);
        UsuarioPapelFazenda::query()->create([
            'id' => UuidV7::gerar()->valor,
            'user_id' => $comprador->id,
            'fazenda_id' => $fazendaId,
            'papel_id' => $mapaPapel[PapelCodigo::Comprador->value],
        ]);

        $aprovador = User::query()->create([
            'uuid' => UuidV7::gerar()->valor,
            'name' => 'Aprovador Demo',
            'email' => 'aprovador@hexavale.local',
            'password' => Hash::make('HexaVale!Apr1'),
            'cpf_cifrado' => $cifrador->cifrar('23100299900'),
            'cpf_blind' => $cifrador->indiceCego('23100299900'),
            'mfa_enabled' => false,
        ]);
        UsuarioPapelFazenda::query()->create([
            'id' => UuidV7::gerar()->valor,
            'user_id' => $aprovador->id,
            'fazenda_id' => $fazendaId,
            'papel_id' => $mapaPapel[PapelCodigo::Aprovador->value],
        ]);

        DB::table('ropa_tratamentos')->insert([
            [
                'id' => UuidV7::gerar()->valor,
                'nome' => 'Contas de usuários do sistema',
                'finalidade' => 'Autenticação e autorização operacional',
                'base_legal' => 'contrato / legítimo interesse (segurança)',
                'categorias_dados' => 'identificação, credenciais, logs de acesso',
                'titulares' => 'colaboradores e prestadores',
                'retencao' => 'enquanto vínculo + 6 meses de logs',
                'compartilhamento' => null,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => UuidV7::gerar()->valor,
                'nome' => 'Operadores de campo',
                'finalidade' => 'Execução de ordens e rastreabilidade',
                'base_legal' => 'execução de contrato',
                'categorias_dados' => 'nome, função, contato operacional',
                'titulares' => 'funcionários / aplicadores',
                'retencao' => '5 anos (ledger/ordens — Q-07)',
                'compartilhamento' => null,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
