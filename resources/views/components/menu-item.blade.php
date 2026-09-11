@props([
    'href' => '#',
    'active' => false
])

<a href="{{ $href }}" 
   class="navegacion-item {{ $active ? 'navegacion-item-activo' : '' }}" 
   wire:navigate>
    <span>{{ $slot }}</span>
</a>