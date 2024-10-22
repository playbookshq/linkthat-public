@props(['compact' => false])

<td {{ $attributes->twMerge(['class' => 'whitespace-nowrap text-sm text-zinc-500 ' . ($compact ? 'py-2 px-2' : 'py-4 px-3')]) }}>
    {{ $slot }}
</td>
