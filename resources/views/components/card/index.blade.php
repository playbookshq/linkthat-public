@props(['class' => ''])

<div {{ $attributes->twMerge(['class' => 'p-6 rounded bg-white shadow-sm dark:bg-white/10 border border-zinc-200 dark:border-white/10 ' . $class]) }}>
    {{ $slot }}
</div>
