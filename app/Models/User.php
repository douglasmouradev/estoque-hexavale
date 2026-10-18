<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use HexaVale\Identidade\Dominio\Permissao as PermissaoDominio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uuid', 'name', 'email', 'password',
        'telefone_cifrado', 'telefone_blind', 'cpf_cifrado', 'cpf_blind',
        'mfa_secret_cifrado', 'mfa_enabled',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password', 'remember_token', 'mfa_secret_cifrado',
        'cpf_cifrado', 'telefone_cifrado',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'mfa_enabled' => 'boolean',
        ];
    }

    /** @return HasMany<UsuarioPapelFazenda, $this> */
    public function papeisFazenda(): HasMany
    {
        return $this->hasMany(UsuarioPapelFazenda::class);
    }

    public function temPermissaoNaFazenda(string $fazendaId, PermissaoDominio $permissao): bool
    {
        $vinculos = $this->papeisFazenda()
            ->where('fazenda_id', $fazendaId)
            ->with('papel.permissoes')
            ->get();

        foreach ($vinculos as $vinculo) {
            $papel = $vinculo->papel;
            if ($papel === null) {
                continue;
            }
            foreach ($papel->permissoes as $p) {
                if ($p->codigo === $permissao->value) {
                    return true;
                }
            }
        }

        return false;
    }
}
