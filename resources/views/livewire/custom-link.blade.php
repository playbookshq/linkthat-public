<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\Block;
use App\Services\UrlCrawlerService;
use Masmerise\Toaster\Toaster;

new class extends Component {
    public $linkPage;

    #[Rule('required|url')]
    public $url = '';

    public function mount($linkPage)
    {
        $this->linkPage = $linkPage;
    }

    public function addLink()
    {
        $this->validate();

        try {
            $crawler = new UrlCrawlerService();
            $result = $crawler->crawl($this->url);

            $maxOrder = $this->linkPage->blocks()->max('order') ?? -1;
            $newBlock = $this->linkPage->blocks()->create([
                'type' => 'link',
                'data' => [
                    'type' => 'custom',
                    'url' => $this->url,
                    'title' => $result['title'] ?: 'Untitled',
                    'description' => $result['description'],
                    'icon' => $result['icon'] ?: null,
                ],
                'order' => $maxOrder + 1,
                'is_visible' => true,
            ]);

            $this->reset('url');
            $this->dispatch('block-created', $newBlock->id);
            Toaster::success('Custom link added successfully!');
        } catch (\Exception $e) {
            Toaster::error('Failed to add the link. Please check the URL and try again.');
        }
    }
};

?>

<div>
    <form wire:submit.prevent="addLink">
        <x-input
            wire:model.live="url"
            placeholder="Type URL and hit enter"
            x-on:keydown.enter.prevent="$wire.addLink()"
        />
    </form>
</div>
