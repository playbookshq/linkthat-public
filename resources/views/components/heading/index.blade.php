@props(['size' => 'base', 'level' => 'div', 'class' => ''])

@php
    $tag = match ($level) {
        'h1' => 'h1',
        'h2' => 'h2',
        'h3' => 'h3',
        default => 'div',
    };

    $classes = match ($size) {
        'xl' => 'text-2xl',
        'lg' => 'text-lg',
        'base' => 'text-base',
        default => 'text-sm',
    };
@endphp

<{{ $tag }} {{ $attributes->twMerge(['class' => $classes . ' font-medium text-zinc-800 dark:text-white ' . $class]) }}>
    {{ $slot }}
</{{ $tag }}>
