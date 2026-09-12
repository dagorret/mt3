<?php

namespace App\Livewire\Unidades;

use App\Actions\Unidades\GestorUnidad;
use App\Models\Unidad;
use Livewire\Component;
use Livewire\WithPagination;

class IndexUnidad extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function eliminar(int $id, GestorUnidad $gestor): void
    {
        $gestor->eliminar($id);
        session()->flash('mensaje', 'Unidad eliminada correctamente.');
    }

    public function render()
    {
        $unidades = Unidad::query()
            ->when($this->search, fn ($q) => $q->where('nombre', 'like', "%{$this->search}%")
                ->orWhere('codigo', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.unidades.index-unidad', compact('unidades'));
    }
}