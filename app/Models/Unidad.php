<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';

    protected $fillable = [
        'nombre',
        'sigla',
        'es_area_tecnica',
        'activa',
    ];

    protected $casts = [
        'es_area_tecnica' => 'boolean',
        'activa' => 'boolean',
    ];

    /**
     * Personas pertenecientes a esta unidad organizativa.
     */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'unidad_id');
    }
}