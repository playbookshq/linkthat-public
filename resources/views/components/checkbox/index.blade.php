@props(['label' => null])

@php
    $id = $attributes->get('id', 'checkbox-' . Str::random(8));
@endphp

<div class="flex items-center">
    <input
        type="checkbox"
        id="{{ $id }}"
        {{ $attributes->twMerge('peer rounded text-zinc-900 disabled:cursor-not-allowed disabled:opacity-50') }}
    />
    @if ($label)
        <label for="{{ $id }}" class="ml-2 text-sm cursor-pointer text-zinc-700 dark:text-zinc-300">
            {{ $label }}
        </label>
    @endif
</div>
