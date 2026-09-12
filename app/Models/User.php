<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasRoles; // Spatie Roles
    use Notifiable;
    use TwoFactorAuthenticatable; // 2FA Local para cuentas con contraseña

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Persona vinculada como sujeto nominal de gobernanza.
     */
    public function persona(): HasOne
    {
        return $this->hasOne(Persona::class, 'user_id');
    }

    /**
     * Obtiene las iniciales del usuario para los avatares de Flux UI.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $segment) => Str::of($segment)->substr(0, 1)->upper())
            ->take(2)
            ->implode('');
    }
}