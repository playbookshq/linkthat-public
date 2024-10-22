<?php

use function Laravel\Folio\name;
use Livewire\Volt\Component;
use App\Services\SeoService;
use App\Models\LinkPage;

name('public-link-page');

new class extends Component {
    public LinkPage $linkPage;

    public function mount($username, SeoService $seo): void
    {
        $this->linkPage = LinkPage::where('username', $username)->firstOrFail();

        $seo->setTitle($this->linkPage->title)
            ->setDescription($this->linkPage->description)
            ->setCanonical(route('public-link-page', ['username' => $username ]));
    }
};
?>

<x-layouts.public>
    @volt('pages.public-link-page')
        <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gradient-to-b">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold">{{ $linkPage->title }}</h1>
                    @if($linkPage->description)
                        <p class="text-lg">{{ $linkPage->description }}</p>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($linkPage->blocks()->orderBy('order')->where('is_visible', true)->get() as $block)
                        @if($block->type === 'separator')
                            <div class="inline-flex items-center justify-center w-full">
                                <hr class="w-full h-px my-4 border-0 bg-zinc-200 dark:bg-zinc-700">
                                <span class="absolute px-3 text-xs font-medium -translate-x-1/2 bg-white text-muted-foreground dark:bg-black left-1/2">
                                    {{ $block->data['text'] }}
                                </span>
                            </div>
                        @elseif($block->type === 'link')
                            <a href="{{ $block->data['url'] }}" target="_blank" rel="noopener noreferrer" class="block w-full">
                                <x-card class="p-3 transition-colors duration-200 bg-white hover:bg-zinc-100 dark:hover:bg-zinc-800">
                                    <x-card.content class="p-4">
                                        <div class="flex items-center space-x-3">
                                            @if($block->data['type'] === 'custom' && isset($block->data['icon']))
                                                <img src="{{ $block->data['icon'] }}" alt="{{ $block->data['title'] }} icon" class="flex-shrink-0 w-6 h-6">
                                            @elseif($block->data['type'] === 'social' && isset($block->data['icon']))
                                                <x-dynamic-component :component="'icons.' . $block->data['icon']" class="flex-shrink-0 w-6 h-6" />
                                            @endif
                                            <div class="flex-grow min-w-0">
                                                <span class="block text-lg font-medium truncate">{{ $block->data['title'] }}</span>
                                                @if(isset($block->data['description']))
                                                    <p class="text-sm truncate text-zinc-600 dark:text-zinc-400">
                                                        {{ $block->data['description'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </x-card.content>
                                </x-card>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.public>
