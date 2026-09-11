<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Bitacora extends Model
{
    use HasFactory;

    protected $table = 'bitacora';

    // Desactivamos updated_at porque la tabla en BD no tiene esa columna
    public const UPDATED_AT = null;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'persona_id',
        'user_id',
        'tipo_evento',
        'justificacion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Modelo polimórfico auditado (Ticket, Tarea, etc.).
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Persona que ejecutó la acción.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Usuario que ejecutó la acción.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}