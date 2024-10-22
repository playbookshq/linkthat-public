<div class="items-center justify-between hidden w-full mr-4 lg:flex">
    <a
        class="flex items-center mr-6 space-x-2"
        wire:navigate
        href="/"
    >
        <div>
            <span class="font-bold">{{config('app.name')}}</span>
        </div>
    </a>
    <nav class="flex items-center gap-4 text-sm lg:gap-6">
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 1</a>
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 2</a>
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 3</a>
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 4</a>
        @if(auth()->check())
            <x-dropdown-menu>
                <x-dropdown-menu.trigger variant="ghost" class="flex items-center justify-between gap-2">
                    <span>{{ auth()->user()->name }}</span>
                    <x-lucide-chevron-down class="self-end size-4 text-foreground/80" />
                </x-dropdown-menu.trigger>

                @volt('layout.navigation.profile.dropdown')
                    <x-dropdown-menu.content>
                        <x-dropdown-menu.item href="{{ route('profile.update') }}" wire:navigate class="flex items-center gap-2">
                            <x-lucide-store class="size-4 text-foreground" />
                            <span>Profile</span>
                        </x-dropdown-menu.item>
                        <x-dropdown-menu.item wire:click='logout' class="flex items-center gap-2">
                            <x-lucide-log-out class="size-4 text-foreground/80" />
                            <span>Logout</span>
                        </x-dropdown-menu.item>
                    </x-dropdown-menu.content>
                @endvolt
            </x-dropdown-menu>
        @endif
    </nav>
</div>
