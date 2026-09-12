@props([
    'labelFrom' => 'Fecha Desde',
    'labelTo' => 'Fecha Hasta',
    'modelFrom' => 'date_from',
    'modelTo' => 'date_to',
])

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <flux:input 
        type="date" 
        :label="$labelFrom" 
        wire:model.live="{{ $modelFrom }}" 
        {{ $attributes }}
    />

    <flux:input 
        type="date" 
        :label="$labelTo" 
        wire:model.live="{{ $modelTo }}" 
        {{ $attributes }}
    />
</div>