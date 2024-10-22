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
        <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gradient-to-b {{ $linkPage->theme === 'dark' ? 'from-gray-900 to-gray-800 text-white' : 'from-gray-100 to-white' }}">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold">{{ $linkPage->title }}</h1>
                    @if($linkPage->description)
                        <p class="text-lg">{{ $linkPage->description }}</p>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($linkPage->links()->orderBy('order')->where('is_visible', true)->get() as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                           class="block w-full p-4 rounded-lg transition-colors duration-200
                                  {{ $linkPage->theme === 'dark' ? 'bg-gray-700 hover:bg-gray-600' : 'bg-white hover:bg-gray-100' }}
                                  shadow-md">
                            <div class="flex items-center">
                                @if($link->type === 'custom' && $link->icon)
                                    <img src="{{ $link->icon }}" alt="{{ $link->title }} icon" class="w-6 h-6 mr-3">
                                @elseif($link->type === 'social' && $link->icon)
                                    <x-dynamic-component :component="'icons.' . $link->icon" class="w-6 h-6 mr-3" />
                                @endif
                                <div>
                                    <span class="text-lg font-medium">{{ $link->title }}</span>
                                    @if($link->description)
                                        <p class="text-sm {{ $linkPage->theme === 'dark' ? 'text-gray-300' : 'text-gray-600' }}">
                                            {{ $link->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.public>
