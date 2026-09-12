<?php

namespace App\Actions\Unidades;

use App\Models\Unidad;
use Illuminate\Support\Facades\DB;

class GestorUnidad
{
    public function guardar(array $datos, ?Unidad $unidad = null): Unidad
    {
        return DB::transaction(function () use ($datos, $unidad) {
            return Unidad::updateOrCreate(
                ['id' => $unidad?->id],
                [
                    'nombre' => $datos['nombre'],
                    'sigla'  => $datos['sigla'],
                ]
            );
        });
    }

    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $unidad = Unidad::findOrFail($id);
            return $unidad->delete();
        });
    }
}