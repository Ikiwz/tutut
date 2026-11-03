@blaze(fold: true)

@aware(['variant'])

@props([
    'current' => false,
    'variant' => 'segmented',
    'href' => null,
    'icon' => null,
])

@php
$classes = Flux::classes('flex whitespace-nowrap flex-1 justify-center items-center gap-2 rounded-md text-sm font-medium transition-all py-1.5 px-3')
    ->add($current
        ? 'bg-white dark:bg-white/20 text-zinc-900 dark:text-white shadow-xs font-semibold'
        : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-800/5 dark:hover:bg-white/5'
    );
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }} data-flux-tab @if ($current) data-current @endif>
        @if ($icon)
            <flux:icon :$icon variant="mini" class="size-4" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->class($classes) }} data-flux-tab @if ($current) data-current @endif>
        @if ($icon)
            <flux:icon :$icon variant="mini" class="size-4" />
        @endif
        {{ $slot }}
    </button>
@endif

