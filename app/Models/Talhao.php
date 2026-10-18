<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Talhao extends Model
{
    use SoftDeletes;

    protected $table = 'talhoes';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'fazenda_id', 'codigo', 'cultura', 'cultivar', 'area_ha',
    ];
}
