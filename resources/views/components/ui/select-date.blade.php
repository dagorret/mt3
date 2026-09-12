@props([
    'label' => 'Fecha',
])

<flux:input 
    type="date" 
    :label="$label" 
    {{ $attributes }} 
/>