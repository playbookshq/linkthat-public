<?php

use Livewire\Volt\Component;
use App\Models\LinkPage;
use App\Models\Link;
use Masmerise\Toaster\Toaster;

new class extends Component {
    public LinkPage $linkPage;
    public $links = [];
    public $showSocialIcons = false;
    public $showCustomLinkForm = false;

    protected $listeners = ['link-created' => 'handleNewLink'];

    public function mount(LinkPage $linkPage)
    {
        $this->linkPage = $linkPage;
        $this->loadLinks();
    }

    public function loadLinks()
    {
        $this->links = $this->linkPage->links()->orderBy('order')->get()->toArray();
    }

    public function handleNewLink($linkId)
    {
        $this->loadLinks();
        $this->showCustomLinkForm = false;
    }

    public function toggleSocialIcons()
    {
        $this->showSocialIcons = !$this->showSocialIcons;
    }

    public function addSocialLink($platform)
    {
        $newLink = $this->linkPage->links()->create([
            'type' => 'social',
            'url' => '',
            'icon' => $platform,
            'title' => '',
            'description' => '',
            'order' => count($this->links),
        ]);

        $this->loadLinks();
        $this->showSocialIcons = false;
        Toaster::success('Social media link added successfully!');
    }

    public function updateLink($linkId)
    {
        $link = Link::findOrFail($linkId);
        $linkIndex = array_search($linkId, array_column($this->links, 'id'));

        if ($linkIndex === false) {
            Toaster::error('Link not found.');
            return;
        }

        $this->validate([
            "links.{$linkIndex}.title" => 'required|string|max:255',
            "links.{$linkIndex}.description" => 'nullable|string|max:1000',
        ]);

        $link->update([
            'title' => $this->links[$linkIndex]['title'],
            'description' => $this->links[$linkIndex]['description'],
            'url' => $this->generateSocialMediaUrl($link->icon, $this->links[$linkIndex]['title']),
        ]);

        Toaster::success('Link updated successfully!');
    }

    private function generateSocialMediaUrl($platform, $username)
    {
        return match ($platform) {
            'x' => "https://x.com/{$username}",
            'facebook' => "https://facebook.com/{$username}",
            'instagram' => "https://instagram.com/{$username}",
            'linkedin' => "https://linkedin.com/in/{$username}",
            'youtube' => "https://youtube.com/@{$username}",
            'tiktok' => "https://tiktok.com/@{$username}",
            default => '',
        };
    }

    public function updateLinkOrder($orderedIds)
    {
        foreach ($orderedIds as $order => $id) {
            Link::where('id', $id)->update(['order' => $order]);
        }
        $this->loadLinks();
        Toaster::success('Link order updated successfully!');
    }

    public function deleteLink($linkId)
    {
        Link::destroy($linkId);
        $this->loadLinks();
        Toaster::success('Link deleted successfully!');
    }
}; ?>

<div>
    <div class="flex items-center mb-4 space-x-4">
        <x-button wire:click="toggleSocialIcons">
            <x-lucide-plus class="w-5 h-5 mr-2" />
            Add Social Media
        </x-button>
        <x-button wire:click="$toggle('showCustomLinkForm')">
            <x-lucide-plus class="w-5 h-5 mr-2" />
            Add Custom Link
        </x-button>
    </div>

    @if($showSocialIcons)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach(['x', 'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok'] as $platform)
                <x-button wire:click="addSocialLink('{{ $platform }}')" class="flex items-center space-x-2">
                    <x-dynamic-component :component="'icons.' . $platform" class="w-5 h-5" />
                    <span>Add {{ ucfirst($platform) }}</span>
                </x-button>
            @endforeach
        </div>
    @endif

    @if($showCustomLinkForm)
        <div class="mb-4">
            <livewire:custom-link :link-page="$linkPage" />
        </div>
    @endif

    <div wire:sortable="updateLinkOrder">
        @foreach($links as $index => $link)
            <x-card wire:key="link-{{ $link['id'] }}" wire:sortable.item="{{ $link['id'] }}" class="mb-2">
                <div class="flex items-center justify-between">
                    <div class="flex-grow" x-data="{ editingTitle: false, editingDescription: false }">
                        <div class="flex items-center space-x-2">
                            @if($link['type'] === 'custom' && $link['icon'])
                                <img src="{{ $link['icon'] }}" alt="Site icon" class="flex-shrink-0 w-5 h-5">
                            @else
                                <x-dynamic-component :component="'icons.' . $link['icon']" class="flex-shrink-0 w-5 h-5" />
                            @endif
                            <div x-show="!editingTitle" @click="editingTitle = true" class="cursor-pointer">
                                <h3 class="font-semibold">{{ $link['title'] ?: 'Click to add title' }}</h3>
                            </div>
                            <input
                                x-show="editingTitle"
                                x-trap="editingTitle"
                                wire:model.live="links.{{ $index }}.title"
                                wire:change="updateLink({{ $link['id'] }})"
                                @click.away="editingTitle = false"
                                @keydown.enter="editingTitle = false"
                                placeholder="Title"
                                class="inline-block px-2 py-1 border-none rounded-md outline-none bg-zinc-100 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:shadow-none"
                            />
                        </div>
                        <div class="mt-1">
                            <p x-show="!editingDescription" @click="editingDescription = true" class="text-sm text-gray-600 cursor-pointer">
                                {{ $link['description'] ?: 'Click to add description' }}
                            </p>
                            <textarea
                                x-show="editingDescription"
                                x-trap="editingDescription"
                                wire:model.live="links.{{ $index }}.description"
                                wire:change="updateLink({{ $link['id'] }})"
                                @click.away="editingDescription = false"
                                placeholder="Description (Optional)"
                                class="w-64 px-2 py-1 mt-1 text-sm border-none rounded-md outline-none bg-zinc-100 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:shadow-none"
                                rows="2"
                            ></textarea>
                        </div>
                    </div>
                    <div class="flex items-center ml-2 space-x-2">
                        <button wire:sortable.handle class="cursor-move">
                            <x-lucide-move class="w-5 h-5" />
                        </button>
                        <button wire:click="deleteLink({{ $link['id'] }})">
                            <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                        </button>
                    </div>
                </div>
            </x-card>
        @endforeach
    </div>
</div>
