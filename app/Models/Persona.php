<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'user_id',
        'unidad_id',
        'nombre',
        'apellido',
        'email',
        'legajo',
        'sexo',
        'ano_nacimiento',
        'movil',
        'interno',
        'oficina',
        'domicilio',
        'cargo',
        'titulo',
        'ano_ingreso',
        'activo',
    ];

    protected $casts = [
        'ano_nacimiento' => 'integer',
        'ano_ingreso' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * Cuenta de usuario del sistema (opcional).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Unidad o área a la que pertenece la persona.
     */
    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class, 'unidad_id');
    }

    /**
     * Accessor para obtener el nombre completo.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }
}