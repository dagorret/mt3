<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Unidades</flux:heading>
            <flux:subheading>Gestión y administración de unidades del sistema.</flux:subheading>
        </div>

        <flux:button href="{{ route('unidades.crear') }}" variant="primary" icon="plus">
            Nueva Unidad
        </flux:button>
    </div>

    <div class="flex items-center justify-between gap-4">
        <div class="w-full max-w-sm">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por nombre o código..." 
                icon="magnifying-glass"
            />
        </div>
    </div>

    @if (session()->has('mensaje'))
        <flux:callout variant="success">
            {{ session('mensaje') }}
        </flux:callout>
    @endif

    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="p-3 font-medium text-zinc-600 dark:text-zinc-400">Código</th>
                    <th class="p-3 font-medium text-zinc-600 dark:text-zinc-400">Nombre</th>
                    <th class="p-3 font-medium text-zinc-600 dark:text-zinc-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse ($unidades as $u)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-900/50">
                        <td class="p-3 font-mono font-medium">{{ $u->sigla ?? '-' }}</td>
                        <td class="p-3">{{ $u->nombre }}</td>
                        <td class="p-3 text-right space-x-2">
                            <flux:button href="{{ route('unidades.editar', $u->id) }}" size="sm" variant="subtle" icon="pencil-square">
                                Editar
                            </flux:button>

                            <flux:button 
                                wire:click="eliminar({{ $u->id }})" 
                                wire:confirm="¿Seguro que querés eliminar esta unidad?"
                                size="sm" 
                                variant="danger" 
                                icon="trash"
                            >
                                Eliminar
                            </flux:button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-6 text-center text-zinc-500">
                            No se encontraron unidades registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $unidades->links() }}
    </div>
</div>