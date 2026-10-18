<?php

declare(strict_types=1);

namespace HexaVale\Auditoria\Infraestrutura;

use App\Models\AuditoriaEvento;
use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Auditoria\Dominio\CadeiaHash;
use HexaVale\Compartilhado\Dominio\UuidV7;
use Illuminate\Support\Facades\DB;

final readonly class EloquentRegistrarAuditoria implements PortaRegistrarAuditoria
{
    public function __construct(private CadeiaHash $cadeia) {}

    public function registrar(
        ?string $fazendaId,
        ?string $atorId,
        string $acao,
        string $entidade,
        string $entidadeId,
        ?array $antes,
        ?array $depois,
        ?string $motivo,
        ?string $ip,
        ?string $userAgent,
    ): string {
        return DB::transaction(function () use (
            $fazendaId, $atorId, $acao, $entidade, $entidadeId,
            $antes, $depois, $motivo, $ip, $userAgent
        ): string {
            $ultimo = AuditoriaEvento::query()->orderByDesc('ocorreu_em')->orderByDesc('id')->lockForUpdate()->first();
            $hashAnterior = $ultimo === null ? $this->cadeia->genesis() : (string) $ultimo->hash;
            $ocorreuEm = now()->format('Y-m-d\TH:i:s.v\Z');
            $payload = json_encode(['antes' => $antes, 'depois' => $depois], JSON_THROW_ON_ERROR);
            $id = UuidV7::gerar()->valor;
            $hash = $this->cadeia->calcular(
                $hashAnterior,
                $ocorreuEm,
                $atorId ?? '',
                $acao,
                $entidade,
                $entidadeId,
                $payload,
            );

            AuditoriaEvento::query()->create([
                'id' => $id,
                'fazenda_id' => $fazendaId,
                'ator_id' => $atorId,
                'acao' => $acao,
                'entidade' => $entidade,
                'entidade_id' => $entidadeId,
                'antes' => $antes,
                'depois' => $depois,
                'motivo' => $motivo,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'ocorreu_em' => $ocorreuEm,
                'hash_anterior' => $hashAnterior,
                'hash' => $hash,
            ]);

            return $id;
        });
    }
}
