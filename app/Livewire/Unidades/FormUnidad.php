<?php

namespace App\Livewire\Unidades;

use App\Actions\Unidades\GestorUnidad;
use App\Models\Unidad;
use Livewire\Component;

class FormUnidad extends Component
{
    public ?Unidad $unidad = null;
    public ?string $nombre = '';
    public ?string $sigla = '';

    public function mount(?Unidad $unidad = null): void
    {
        if ($unidad && $unidad->exists) {
            $this->unidad = $unidad;
            $this->nombre = $unidad->nombre;
            $this->sigla = $unidad->sigla;
        }
    }

    protected function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'sigla'  => 'nullable|string|max:20',
        ];
    }

    public function guardar(GestorUnidad $gestor)
    {
        $datos = $this->validate();

        $gestor->guardar($datos, $this->unidad);

        session()->flash('mensaje', 'Unidad guardada exitosamente.');

        return redirect()->route('unidades.index');
    }

    public function render()
    {
        return view('livewire.unidades.form-unidad');
    }
}