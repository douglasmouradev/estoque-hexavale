<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class AuditoriaEvento extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $table = 'auditoria';

    protected $fillable = [
        'id', 'fazenda_id', 'ator_id', 'acao', 'entidade', 'entidade_id',
        'antes', 'depois', 'motivo', 'ip', 'user_agent', 'ocorreu_em',
        'hash_anterior', 'hash',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'antes' => 'array',
            'depois' => 'array',
            'ocorreu_em' => 'datetime',
        ];
    }
}
