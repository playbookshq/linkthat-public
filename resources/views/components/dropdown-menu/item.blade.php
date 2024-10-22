@props([
    'disabled' => false,
    'inset' => false,
    'href' => null,
])

<li
    role="menuitem"
    aria-disabled="{{ $disabled ? 'true' : 'false' }}"
    x-dropdown-menu:item
    tabindex="-1"
    {{ $attributes->except(['href', 'wire:navigate', 'class'])->when($disabled, function ($attributes) {
            return $attributes->except(['x-on:click', '@click', 'wire:click']);
        })->twMerge([
            'hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground',
            'relative w-full cursor-default select-none',
            'rounded-sm px-3 py-2 text-sm outline-none transition-colors',
            'opacity-50 cursor-not-allowed' => $disabled,
            'pl-8' => $inset,
        ]) }}
>
    @if ($href)
        <a href="{{ $href }}" {{ $attributes->only(['wire:navigate', 'class']) }} class="flex items-center w-full">
            {{ $slot }}
        </a>
    @else
        <div {{ $attributes->only('class') }} class="flex items-center w-full">
            {{ $slot }}
        </div>
    @endif
</li>
