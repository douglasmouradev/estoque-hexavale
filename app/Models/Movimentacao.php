<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Movimentacao extends Model
{
    protected $table = 'movimentacoes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'fazenda_id', 'lote_id', 'tipo', 'quantidade', 'unidade', 'motivo',
        'referencia_tipo', 'referencia_id', 'ator_id', 'idempotency_key', 'ocorreu_em',
    ];
}
