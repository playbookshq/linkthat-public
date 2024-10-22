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
            ->setCanonical(route('public-link-page', $username));
    }
};
?>

<x-layouts.app>
    @volt('pages.public-link-page')
        <div>
            <h1 class="mb-4 text-2xl font-bold">{{ $linkPage->title }}</h1>
            <!-- Add public link page content here -->
        </div>
    @endvolt
</x-layouts.app>
