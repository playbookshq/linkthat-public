<?php
use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use App\Models\LinkPage;
use App\Services\SeoService;

name('home');

new class extends Component {
    public ?LinkPage $linkPage = null;
    public $visibleBlocks = [];
    public $visibleCount = 0;
    public $startPolling = false;
    public $stopPolling = false;

    public function mount(SeoService $seo): void
    {
        $this->linkPage = LinkPage::where('username', 'ian')->first();
        $this->visibleBlocks = $this->getVisibleBlocks();

        $seo->setTitle('Linkthat - One link in bio for everything you do online')
            ->setDescription('Linkthat is the link in bio tool that doesn\'t do too much. Add your social profiles, websites, and products then share one link. It\'s that simple.')
            ->setCanonical(route('home'))
            ->setOgImage(asset('images/og-image.jpg'));
    }

    public function getVisibleBlocks()
    {
        if (!$this->linkPage) {
            return [];
        }

        return $this->linkPage->blocks()
            ->where('is_visible', true)
            ->where('type', 'link')
            ->orderBy('order')
            ->take(4)
            ->get();
    }

    public function showFirstBlock()
    {
        $this->visibleCount = 1;
        $this->startPolling = true;
    }

    public function showNextBlock()
    {
        if ($this->visibleCount < count($this->visibleBlocks)) {
            $this->visibleCount++;
        } else {
            $this->stopPolling = true;
        }
    }
};
?>

<x-layouts.app>
    @volt('pages.home')
    <div>
        <div class="max-w-xl py-12 mx-auto space-y-6 text-center sm:px-4">
            <h1 class="text-3xl font-bold leading-10 tracking-tight text-zinc-900 dark:text-zinc-100 lg:text-5xl md:text-center">
                One link in bio for everything you do online
            </h1>
            <p class="mx-auto mt-5 text-zinc-700 dark:text-zinc-400 md:mt-8 md:max-w-lg md:text-center md:text-xl">
                Linkthat is the link in bio tool that doesn't do too much. Add your social profiles, websites, and products then share one link. It's that simple.
            </p>
            <p class="mx-auto mt-5 text-zinc-400 dark:text-zinc-500 md:mt-8 md:max-w-lg md:text-center md:text-xl">
                Soon, you'll also be able customize your bio, track your clicks and use your own domain.
            </p>
            <div class="flex items-center justify-center space-x-4">
                <x-button size="lg" href="{{ route('register') }}">
                    Get started for free &rarr;
                </x-button>
            </div>
        </div>

        <div class="max-w-md mx-auto">
            <div
                class="space-y-4"
                wire:init="showFirstBlock"
                @if($startPolling && !$stopPolling)
                    wire:poll.1500ms="showNextBlock"
                @endif
            >
                @foreach($visibleBlocks as $index => $block)
                    <div
                        wire:key="block-{{ $block->id }}"
                        class="w-full transition-all duration-300 ease-in-out {{ $index < $visibleCount ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4' }}"
                    >
                    <a href="{{ $block->data['url'] }}" target="_blank" rel="noopener noreferrer" class="block w-full">
                        <x-card class="p-3 transition-colors duration-200 bg-white hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <x-card.content class="p-4">
                                <div class="flex items-center space-x-3">
                                    @if(data_get($block, 'data.type') === 'custom' && data_get($block, 'data.icon'))
                                        <img src="{{ data_get($block, 'data.icon') }}" alt="{{ data_get($block, 'data.title', 'Icon') }}" class="flex-shrink-0 w-6 h-6">
                                    @elseif(data_get($block, 'data.type') === 'social' && data_get($block, 'data.icon'))
                                        <x-dynamic-component :component="'icons.' . data_get($block, 'data.icon')" class="flex-shrink-0 w-6 h-6" />
                                    @endif
                                    <div class="flex-grow min-w-0">
                                        <span class="block text-lg font-medium truncate">{{ data_get($block, 'data.title', 'Untitled') }}</span>
                                        @if(data_get($block, 'data.description'))
                                            <p class="text-sm truncate text-zinc-600 dark:text-zinc-400">
                                                {{ data_get($block, 'data.description') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </x-card.content>
                        </x-card>
                    </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endvolt
</x-layouts.app>
