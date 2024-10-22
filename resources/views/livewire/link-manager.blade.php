<?php

use Livewire\Volt\Component;
use App\Models\LinkPage;
use App\Models\Block;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Gate;

new class extends Component {
    public LinkPage $linkPage;
    public $blocks = [];
    public $activeInput = null; // New property to track active input
    public $canUseCustomLinks;
    public $separatorText = '';

    protected $listeners = ['block-created' => 'handleNewBlock'];

    public function mount(LinkPage $linkPage)
    {
        $this->linkPage = $linkPage;
        $this->loadBlocks();
        $this->canUseCustomLinks = Gate::allows('use-custom-link-cards');
    }

    public function loadBlocks()
    {
        $this->blocks = $this->linkPage->blocks()
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function handleNewBlock($blockId)
    {
        $this->loadBlocks();
    }

    public function toggleInput($input)
    {
        $this->activeInput = ($this->activeInput === $input) ? null : $input;
    }

    public function toggleCustomLinkInput()
    {
        if (!$this->canUseCustomLinks) {
            $this->showUpgradeMessage();
            return;
        }
        $this->toggleInput('custom');
    }

    public function addSocialLink($platform)
    {
        $maxOrder = $this->linkPage->blocks()->max('order') ?? -1;
        $newBlock = $this->linkPage->blocks()->create([
            'type' => 'link',
            'data' => [
                'type' => 'social',
                'icon' => $platform,
                'title' => '',
                'description' => '',
                'url' => '',
            ],
            'order' => $maxOrder + 1,
            'is_visible' => true,
        ]);

        $this->loadBlocks();
        $this->showSocialIcons = false;
        Toaster::success('Social media link added successfully!');
    }

    public function updateBlock($blockId)
    {
        $block = Block::findOrFail($blockId);
        $blockIndex = array_search($blockId, array_column($this->blocks, 'id'));

        if ($blockIndex === false) {
            Toaster::error('Block not found.');
            return;
        }

        $this->validate([
            "blocks.{$blockIndex}.data.title" => 'required|string|max:255',
            "blocks.{$blockIndex}.data.description" => 'nullable|string|max:1000',
        ]);

        $updatedData = array_merge($block->data, [
            'title' => $this->blocks[$blockIndex]['data']['title'],
            'description' => $this->blocks[$blockIndex]['data']['description'],
        ]);

        if ($block->data['type'] === 'social') {
            $updatedData['url'] = $this->generateSocialMediaUrl($block->data['icon'], $this->blocks[$blockIndex]['data']['title']);
        }

        $block->update([
            'data' => $updatedData,
        ]);

        Toaster::success('Block updated successfully!');
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

    public function updateLinkOrder($list)
    {
        foreach ($list as $item) {
            $block = $this->linkPage->blocks()->find($item['value']);
            if ($block) {
                $block->update([
                    'order' => $item['order']
                ]);
            }
        }
        $this->loadBlocks();
    }

    public function deleteBlock($blockId)
    {
        Block::destroy($blockId);
        $this->loadBlocks();
        Toaster::success('Block deleted successfully!');
    }

    public function toggleBlockVisibility($blockId)
    {
        $block = Block::findOrFail($blockId);
        $blockIndex = array_search($blockId, array_column($this->blocks, 'id'));

        if ($blockIndex === false) {
            Toaster::error('Block not found.');
            return;
        }

        $block->update([
            'is_visible' => $this->blocks[$blockIndex]['is_visible']
        ]);

        Toaster::success('Block visibility updated successfully!');
    }

    public function showUpgradeMessage()
    {
        Toaster::info('Custom link cards are available for premium users. Upgrade now to unlock this feature.');
    }

    public function addLink($type, $data)
    {
        $maxOrder = $this->linkPage->blocks()->max('order') ?? -1;
        $newBlock = $this->linkPage->blocks()->create([
            'type' => 'link',
            'data' => array_merge(['type' => $type], $data),
            'order' => $maxOrder + 1,
            'is_visible' => true,
        ]);

        $this->loadBlocks();
        Toaster::success('Link added successfully!');
    }

    public function addSeparator()
    {
        $text = trim($this->separatorText) ?: 'Separator';
        $maxOrder = $this->linkPage->blocks()->max('order') ?? -1;
        $newBlock = $this->linkPage->blocks()->create([
            'type' => 'separator',
            'data' => ['text' => $text],
            'order' => $maxOrder + 1,
            'is_visible' => true,
        ]);

        $this->loadBlocks();
        $this->separatorText = '';
        Toaster::success('Separator "' . $text . '" added successfully!');
    }
}; ?>

<div>
    <div class="mb-4">
        <h2 class="text-xl font-semibold">Manage Links</h2>
    </div>

    <div class="flex flex-wrap gap-2 mb-4">
        <x-button wire:click="toggleInput('social')" size="sm">
            Add Social Link
        </x-button>
        <x-button wire:click="toggleCustomLinkInput" size="sm">
            Add Custom URL
        </x-button>
        <x-button wire:click="toggleInput('separator')" size="sm">
            Add Separator
        </x-button>
    </div>

    @if($activeInput === 'social')
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach(['x', 'facebook', 'instagram', 'linkedin', 'youtube', 'tiktok'] as $platform)
                <x-button variant="secondary" size="sm" wire:click="addSocialLink('{{ $platform }}')" class="flex items-center space-x-2">
                    <x-dynamic-component :component="'icons.' . $platform" class="w-5 h-5" />
                    <span>Add {{ ucfirst($platform) }}</span>
                </x-button>
            @endforeach
        </div>
    @endif

    @if($activeInput === 'custom' && $canUseCustomLinks)
        <div class="mb-4">
            <livewire:custom-link :link-page="$linkPage" />
        </div>
    @endif

    @if($activeInput === 'separator')
        <div class="mb-4">
            <form wire:submit.prevent="addSeparator">
                <div class="flex items-center space-x-2">
                    <x-input
                        wire:model="separatorText"
                        placeholder="Separator text"
                    />
                    <x-button type="submit">
                        Add
                    </x-button>
                </div>
            </form>
        </div>
    @endif

    <div wire:sortable="updateLinkOrder" wire:sortable.options="{ animation: 100 }">
        @foreach ($blocks as $index => $block)
            <div wire:key="block-{{ $block['id'] }}" wire:sortable.item="{{ $block['id'] }}">
                @if($block['type'] === 'separator')
                    <div class="relative mb-4 cursor-move" wire:sortable.handle>
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-between">
                            <span class="px-2 text-sm text-gray-500 bg-white">{{ $block['data']['text'] }}</span>
                            <button wire:click="deleteBlock({{ $block['id'] }})" class="px-2 text-sm text-red-500 bg-white">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                @else
                    <x-card class="mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if($block['data']['type'] === 'custom' && $block['data']['icon'])
                                    <img src="{{ $block['data']['icon'] }}" alt="Site icon" class="flex-shrink-0 w-5 h-5">
                                @else
                                    <x-dynamic-component :component="'icons.' . $block['data']['icon']" class="flex-shrink-0 w-5 h-5" />
                                @endif
                                <div x-data="{ editingTitle: false, editingDescription: false }" x-id="['block-edit-{{ $block['id'] }}']">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="font-semibold" x-show="!editingTitle" @click="editingTitle = true">
                                            {{ $block['data']['title'] ?: 'Click to add title' }}
                                        </h3>
                                        <input
                                            x-show="editingTitle"
                                            x-trap="editingTitle"
                                            wire:model="blocks.{{ $index }}.data.title"
                                            wire:change="updateBlock({{ $block['id'] }})"
                                            @click.away="editingTitle = false"
                                            @keydown.enter="editingTitle = false"
                                            :id="$id('block-edit-{{ $block['id'] }}')"
                                            placeholder="Title"
                                            class="inline-block px-2 py-1 border-none rounded-md outline-none bg-zinc-100 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:shadow-none"
                                        />
                                    </div>
                                    <div class="mt-1">
                                        <p x-show="!editingDescription" @click="editingDescription = true" class="text-sm text-gray-600 cursor-pointer">
                                            {{ $block['data']['description'] ?: 'Click to add description' }}
                                        </p>
                                        <textarea
                                            x-show="editingDescription"
                                            x-trap="editingDescription"
                                            wire:model="blocks.{{ $index }}.data.description"
                                            wire:change="updateBlock({{ $block['id'] }})"
                                            @click.away="editingDescription = false"
                                            :id="$id('block-edit-{{ $block['id'] }}')"
                                            placeholder="Description (Optional)"
                                            class="w-64 px-2 py-1 mt-1 text-sm border-none rounded-md outline-none bg-zinc-100 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:shadow-none"
                                            rows="2"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center ml-2 space-x-2">
                                <button wire:sortable.handle class="cursor-move">
                                    <x-lucide-move class="w-5 h-5" />
                                </button>
                                <button wire:click="deleteBlock({{ $block['id'] }})">
                                    <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                                </button>
                                <x-switch
                                    wire:model="blocks.{{ $index }}.is_visible"
                                    wire:change="toggleBlockVisibility({{ $block['id'] }})"
                                    :checked="$block['is_visible']"
                                />
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>
        @endforeach
    </div>
</div>
