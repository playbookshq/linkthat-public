<div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
    <table {{ $attributes->twMerge(['class' => 'min-w-full divide-y divide-zinc-300']) }}>
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
