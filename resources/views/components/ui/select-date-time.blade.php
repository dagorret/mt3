@props([
    'label' => 'Fecha y Hora',
])

<flux:input 
    type="datetime-local" 
    :label="$label" 
    {{ $attributes }} 
/>