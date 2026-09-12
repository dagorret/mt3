@props([
    'label' => 'Hora',
])

<flux:input 
    type="time" 
    :label="$label" 
    {{ $attributes }} 
/>