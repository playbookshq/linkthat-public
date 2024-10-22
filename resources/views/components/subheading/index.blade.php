@props(['size' => null, 'class' => ''])

@php
$classes = match ($size) {
    'xl' => 'text-lg',
    'lg' => 'text-base',
    'sm' => 'text-xs',
    default => 'text-sm',
};

$classes = trim($classes . ' text-zinc-500 dark:text-white/70 ' . $class);
@endphp

<div {{ $attributes->class($classes)->twMerge([]) }}>
    {{ $slot }}
</div>
