@props([
    'name' => null,
    'maxWidth' => '2xl',
    'showCloseButton' => true
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: false,
        name: '{{ $name }}',
        pulsing: false,
        triggerPulse() {
            this.pulsing = true;
            setTimeout(() => this.pulsing = false, 600);
        }
    }"
    x-on:modal-show.window="if ($event.detail.name === name) { show = true; $nextTick(() => { triggerPulse(); }) }"
    x-on:modal-hide.window="if ($event.detail.name === name) { show = false }"
    x-on:keydown.escape.window="show = false"
    x-on:click.self="show = false"
>
    <div x-show="show" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-opacity-75 bg-zinc-500"
                aria-hidden="true"
            ></div>

            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-bind:class="{ 'animate-pulse-scale': pulsing }"
                class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white text-left align-middle shadow-xl transition-all sm:w-full {{ $maxWidth }}"
                @click.away="show = false"
            >
                <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    @if($showCloseButton)
                        <div class="absolute top-0 right-0 pt-4 pr-4">
                            <x-modal.close>
                            <button
                                type="button"
                                class="transition duration-150 ease-in-out text-zinc-300 hover:text-zinc-400 focus:outline-none focus:text-zinc-400"
                            >
                                <span class="sr-only">Close</span>
                                <x-lucide-x class="w-6 h-6" />
                            </button>
                        </x-modal.close>
                        </div>
                    @endif
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
