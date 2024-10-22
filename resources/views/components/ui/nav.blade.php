<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout() : void
    {
        Auth::logout();

        $this->redirect('/', navigate: true);
    }
};
?>

<div class="flex items-center justify-between w-full lg:flex">
    <a
        class="flex items-center space-x-2"
        wire:navigate
        href="/"
    >
        <div class="flex items-center space-x-2">
            <x-logo class="size-5" />
            <span class="font-bold">{{config('app.name')}}</span>
        </div>
    </a>
    <nav class="flex items-center gap-4 text-sm lg:gap-6">
        {{-- <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 1</a>
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 2</a>
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 3</a>
        <a
            class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
            wire:navigate
            href="/"
        >Example 4</a> --}}
        @if(auth()->check())
            <a
                class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
                wire:navigate
                href="{{ route('dashboard') }}"
            >
                Dashboard
            </a>
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
            @else
            <a
                class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
                wire:navigate
                href="{{ route('login') }}"
            >
                Login
            </a>
            <a
                class="px-3 py-2 transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-foreground/80 text-foreground/60"
                wire:navigate
                href="{{ route('register') }}"
            >
                Sign up &rarr;
            </a>
        @endif
    </nav>
</div>
