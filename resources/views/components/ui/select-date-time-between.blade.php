@props([
    'labelFrom' => 'Desde',
    'labelTo' => 'Hasta',
    'modelFrom' => 'datetime_from',
    'modelTo' => 'datetime_to',
])

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <flux:input 
        type="datetime-local" 
        :label="$labelFrom" 
        wire:model.live="{{ $modelFrom }}" 
        {{ $attributes }}
    />

    <flux:input 
        type="datetime-local" 
        :label="$labelTo" 
        wire:model.live="{{ $modelTo }}" 
        {{ $attributes }}
    />
</div>