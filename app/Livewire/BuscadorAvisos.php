<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class BuscadorAvisos extends Component
{
    // ESTADO: viaja en cada peticion dentro del snapshot
    public string $busqueda = '';

    // ACCION: se llama desde la vista con wire:click="limpiar"
    public function limpiar(): void
    {
        $this->busqueda = '';
    }

    // Hook: cada vez que cambia $busqueda (por si mas adelante paginas)
    public function updatedBusqueda(): void
    {
        // $this->resetPage();
    }

    // LA VISTA: se vuelve a pintar despues de cada accion o cambio
    public function render()
    {
        return view('livewire.buscador-avisos', [
            'avisos' => Post::publicados()
                ->with('categoria')
                ->where('titulo', 'like', "%{$this->busqueda}%")
                ->latest()
                ->get(),
        ]);
    }
}
