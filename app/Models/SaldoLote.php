<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class SaldoLote extends Model
{
    protected $table = 'saldos_lote';

    protected $primaryKey = 'lote_id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'lote_id', 'fazenda_id', 'fisico', 'reservado', 'atualizado_em',
    ];
}
