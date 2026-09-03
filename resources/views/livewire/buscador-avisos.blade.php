{{-- Un solo elemento raiz: es lo que Livewire vigila y actualiza por morph. --}}
<div>
    <div class="flex gap-2">
        <input type="text"
               wire:model.live.debounce.300ms="busqueda"
               placeholder="Buscar aviso"
               class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-200 outline-none">
        <button type="button" wire:click="limpiar"
                class="rounded-lg border border-gray-300 px-4 py-2 text-gray-600 hover:bg-gray-50">
            Limpiar
        </button>
    </div>

    {{-- Se muestra solo mientras hay una peticion en curso --}}
    <p wire:loading class="mt-2 text-sm text-gray-500">Buscando...</p>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        @forelse ($avisos as $aviso)
            {{-- wire:key le dice a Livewire que tarjeta es cual cuando la lista cambia --}}
            <x-tarjeta-post :post="$aviso" wire:key="aviso-{{ $aviso->id }}" />
        @empty
            <p class="text-gray-500">No hay avisos que coincidan con "{{ $busqueda }}".</p>
        @endforelse
    </div>
</div>
