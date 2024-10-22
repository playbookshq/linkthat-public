<div class="overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5">
    <table {{ $attributes->twMerge(['class' => 'min-w-full divide-y divide-zinc-300 dark:divide-zinc-700']) }}>
        @isset($head)
            <x-table.head>
                {{ $head }}
            </x-table.head>
        @endisset

        {{ $slot }}

        @isset($foot)
            <x-table.foot>
                {{ $foot }}
            </x-table.foot>
        @endisset
    </table>
</div>
