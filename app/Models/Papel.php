<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Papel extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'papeis';

    protected $fillable = ['id', 'codigo', 'nome'];

    /** @return BelongsToMany<Permissao, $this> */
    public function permissoes(): BelongsToMany
    {
        return $this->belongsToMany(Permissao::class, 'papel_permissao');
    }
}
