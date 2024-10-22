@props(['label' => null])

@php
    $id = $attributes->get('id', 'checkbox-' . Str::random(8));
@endphp

<div class="flex items-center">
    <input
        type="checkbox"
        id="{{ $id }}"
        {{ $attributes->twMerge('peer rounded text-black disabled:cursor-not-allowed disabled:opacity-50') }}
    />
    @if ($label)
        <label for="{{ $id }}" class="ml-2 text-sm text-gray-700 cursor-pointer">
            {{ $label }}
        </label>
    @endif
</div>
