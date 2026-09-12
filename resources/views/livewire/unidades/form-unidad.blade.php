<div class="max-w-xl space-y-6">
    <div>
        <flux:heading size="xl">
            {{ $unidad && $unidad->exists ? 'Editar Unidad' : 'Nueva Unidad' }}
        </flux:heading>
        <flux:subheading>
            Completá los datos para {{ $unidad && $unidad->exists ? 'actualizar' : 'registrar' }} la unidad.
        </flux:subheading>
    </div>

    <form wire:submit="guardar" class="space-y-6">
        <flux:field>
            <flux:label>Nombre</flux:label>
            <flux:input wire:model.live="nombre" placeholder="Ej: Kilogramos" />
            <flux:error name="nombre" />
        </flux:field>

        <flux:field>
            <flux:label>Código</flux:label>
            <flux:input wire:model.live="sigla" placeholder="Ej: KG" />
            <flux:error name="sigla" />
        </flux:field>

        <div class="flex items-center justify-end gap-3 pt-4">
            <flux:button href="{{ route('unidades.index') }}" variant="ghost">
                Cancelar
            </flux:button>

            <flux:button type="submit" variant="primary">
                Guardar
            </flux:button>
        </div>
    </form>
</div>