<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Services\SeoService;
use App\Models\LinkPage;

middleware(['auth', 'verified']);
name('link-pages.edit');

new class extends Component {
    public LinkPage $linkPage;

    public function mount($id, SeoService $seo): void
    {
        $this->linkPage = LinkPage::findOrFail($id);

        $seo->setTitle("Edit {$this->linkPage->title}")
            ->setDescription('Edit your link page.')
            ->setCanonical(route('link-pages.edit', $id));
    }
};
?>

<x-layouts.app>
    @volt('pages.link-pages.edit')
        <div>
            <h1 class="mb-4 text-2xl font-bold">Edit Link Page: {{ $linkPage->title }}</h1>
            <!-- Add link page editor content here -->
        </div>
    @endvolt
</x-layouts.app>
