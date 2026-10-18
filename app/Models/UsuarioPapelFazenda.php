<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UsuarioPapelFazenda extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'usuario_papel_fazenda';

    protected $fillable = ['id', 'user_id', 'fazenda_id', 'papel_id'];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Fazenda, $this> */
    public function fazenda(): BelongsTo
    {
        return $this->belongsTo(Fazenda::class);
    }

    /** @return BelongsTo<Papel, $this> */
    public function papel(): BelongsTo
    {
        return $this->belongsTo(Papel::class);
    }
}
