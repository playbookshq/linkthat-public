@props(['compact' => false])

<th {{ $attributes->twMerge(['class' => 'text-left font-medium bg-black text-white ' . ($compact ? 'px-2 py-2 text-xs' : 'px-3 py-3.5 text-sm')]) }} scope="col">
    {{ $slot }}
</th>
