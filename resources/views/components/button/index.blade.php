@props([
    'variant' => null,
    'size' => null,
    'type' => 'button',
    'href' => null,
])

@inject('button', 'App\Services\ButtonCvaService')

@php
    $tag = $href ? 'a' : 'button';
    $attrs = $attributes->twMerge($button(['variant' => $variant, 'size' => $size]));

    if ($href) {
        $attrs = $attrs->merge(['href' => $href]);
    } else {
        $attrs = $attrs->merge(['type' => $type]);
    }
@endphp

<{{ $tag }} {{ $attrs }}>
    {{ $slot }}
</{{ $tag }}>
