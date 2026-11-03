@blaze(fold: true)

@props([
    'variant' => 'default',
    'size' => null,
])

@php
$classes = Flux::classes()
    ->add(match ($variant) {
        'segmented' => 'flex p-1 rounded-lg bg-zinc-800/5 dark:bg-white/10 w-full gap-1',
        default => 'flex items-center gap-1 border-b border-zinc-200 dark:border-zinc-800',
    });
@endphp

<div {{ $attributes->class($classes) }} data-flux-tabs data-flux-tabs-variant="{{ $variant }}">
    {{ $slot }}
</div>
