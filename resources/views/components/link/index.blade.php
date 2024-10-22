@props([
    'variant' => null,
    'external' => null,
])

@php
$classes = trim(match ($variant) {
    'ghost' => 'no-underline hover:underline text-zinc-800',
    'subtle' => 'no-underline text-zinc-500 hover:text-zinc-800',
    default => 'underline text-zinc-800',
});

$classes = 'inline text-inherit font-medium ' . $classes;
@endphp

<a {{ $attributes->class($classes)->twMerge([]) }} <?php if ($external) : ?>target="_blank"<?php endif; ?>>{{ $slot }}</a>


