<?php

use Livewire\Volt\Component;
use App\Models\LinkPage;
use App\Models\Link;
use Masmerise\Toaster\Toaster;

new class extends Component {
    public LinkPage $linkPage;
    public $links = [];
    public $newLinkType = '';
    public $newLink = [
        'type' => 'social',
        'icon' => '',
        'title' => '',
        'description' => '',
    ];

    public function mount(LinkPage $linkPage)
    {
        $this->linkPage = $linkPage;
        $this->loadLinks();
    }

    public function loadLinks()
    {
        $this->links = $this->linkPage->links()->orderBy('order')->get()->toArray();
    }

    public function addLink()
    {
        $this->validate([
            'newLink.icon' => 'required|string|max:255',
            'newLink.title' => 'required|string|max:255',
            'newLink.description' => 'nullable|string|max:1000',
        ]);

        $url = $this->generateSocialMediaUrl($this->newLink['icon'], $this->newLink['title']);

        $this->linkPage->links()->create([
            'type' => 'social',
            'url' => $url,
            'icon' => $this->newLink['icon'],
            'title' => $this->newLink['title'],
            'description' => $this->newLink['description'],
        ]);

        $this->loadLinks();
        $this->reset(['newLink', 'newLinkType']);
        Toaster::success('Social media link added successfully!');
    }

    private function generateSocialMediaUrl($platform, $username)
    {
        return match ($platform) {
            'twitter' => "https://twitter.com/{$username}",
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
    <div class="mb-4">
        <x-button wire:click="$set('newLinkType', 'social')">Add Social Media Link</x-button>
    </div>

    @if($newLinkType === 'social')
        <x-card class="mb-4">
            <h3 class="mb-2 text-lg font-semibold">Add New Social Media Link</h3>
            <x-form wire:submit.prevent="addLink">
                <x-form.item>
                    <x-label for="newLink.icon">Platform</x-label>
                    <x-select wire:model="newLink.icon" id="newLink.icon" required>
                        <option value="">Select a platform</option>
                        <option value="twitter">Twitter</option>
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="linkedin">LinkedIn</option>
                        <option value="youtube">YouTube</option>
                        <option value="tiktok">TikTok</option>
                    </x-select>
                    <x-form.message for="newLink.icon" />
                </x-form.item>

                <x-form.item>
                    <x-label for="newLink.title">Username/Handle</x-label>
                    <x-input wire:model="newLink.title" id="newLink.title" type="text" required />
                    <x-form.message for="newLink.title" />
                </x-form.item>

                <x-form.item>
                    <x-label for="newLink.description">Description (Optional)</x-label>
                    <x-textarea wire:model="newLink.description" id="newLink.description" />
                    <x-form.message for="newLink.description" />
                </x-form.item>

                <x-button type="submit">Add Link</x-button>
            </x-form>
        </x-card>
    @endif

    <div wire:sortable="updateLinkOrder">
        @foreach($links as $link)
            <x-card wire:key="link-{{ $link['id'] }}" wire:sortable.item="{{ $link['id'] }}" class="mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold">{{ $link['icon'] }} - {{ $link['title'] }}</h3>
                        @if($link['description'])
                            <p class="text-sm text-gray-600">{{ $link['description'] }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
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
