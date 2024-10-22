<div
    role="status"
    id="toaster"
    x-data="toasterHub(@js($toasts), @js($config))"
    @class([
        'fixed z-50 p-4 w-full flex flex-col pointer-events-none sm:p-6',
        'bottom-0' => $alignment->is('bottom'),
        'top-0' => $alignment->is('top'),
        'items-end' => $position->is('right'),
        'items-center' => $position->is('center'),
        'items-start' => $position->is('left'),
    ])
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.isVisible"
            x-init="$nextTick(() => toast.show($el))"
            x-transition:enter-start="opacity-0 translate-y-5"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90"
            class="bg-white border shadow-sm pointer-events-auto border-zinc-200 rounded-xl"
        >
            <div class="flex items-center gap-4 p-4">
                <div class="flex-1">
                    <div x-html="toast.message" class="text-sm text-zinc-800 text-medium"></div>
                </div>

                @if ($closeable)
                    <button
                        @click="toast.dispose()"
                        class="flex items-center justify-center w-8 h-8 rounded-md text-zinc-400 hover:text-zinc-800 hover:bg-zinc-100"
                    >
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </template>
</div>
