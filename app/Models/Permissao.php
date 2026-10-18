<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Permissao extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'permissoes';

    protected $fillable = ['id', 'codigo', 'descricao'];
}
