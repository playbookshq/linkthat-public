<?php

use function Laravel\Folio\{name, middleware};
use Livewire\Volt\Component;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;

name('dashboard');
middleware(['auth']);

new class extends Component {
    public $linkPages;
    public $linkPageToDelete = null;

    public function mount(SeoService $seo): void
    {
        $this->linkPages = Auth::user()->linkPages;

        $seo->setTitle('Dashboard')
            ->setDescription('Manage your LinkPages.')
            ->setCanonical(route('dashboard'));
    }

    public function deleteLinkPage($linkPageId)
    {
        $this->linkPageToDelete = $linkPageId;
        if ($this->linkPageToDelete) {
            try {
                $linkPage = LinkPage::findOrFail($this->linkPageToDelete);

                if ($linkPage->user_id !== Auth::id()) {
                    throw new \Exception('You are not authorized to delete this Link page.');
                }

                // Delete associated links
                $linkPage->links()->delete();

                // Delete the LinkPage
                $linkPage->delete();

                $this->linkPages = Auth::user()->linkPages;
                $this->linkPageToDelete = null;

                Toaster::success('Link page deleted successfully.');
            } catch (\Exception $e) {
                Toaster::error('Error deleting Link page: ' . $e->getMessage());
            }
        }
    }
};

?>

<x-layouts.app>
    @volt('pages.dashboard')
        <div class="max-w-3xl p-3 mx-auto">
            <div class="mb-6">
                <h1 class="text-2xl font-medium">Your link pages</h1>
                <p class="pt-0 !mt-0 text-zinc-700 dark:text-zinc-400">
                    Manage all of your link pages with easy drag and drop ordering.
                </p>
            </div>
            <div class="mb-6">
                @if(!$linkPages->isEmpty())
                    <x-table>
                        <x-slot name="head">
                            <x-table.row>
                                <x-table.heading>Title</x-table.heading>
                                <x-table.heading>Username</x-table.heading>
                                <x-table.heading>Icons</x-table.heading>
                                <x-table.heading>Actions</x-table.heading>
                            </x-table.row>
                        </x-slot>

                        <x-table.body>
                            @foreach($linkPages as $linkPage)
                                <x-table.row>
                                    <x-table.cell>
                                        <a href="{{ route('link-pages.edit', ['id' => $linkPage->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline"
                                        wire:navigate
                                            >
                                            {{ $linkPage->title }}
                                        </a>
                                    </x-table.cell>
                                    <x-table.cell>{{ $linkPage->username }}</x-table.cell>
                                    <x-table.cell>
                                        <div class="flex space-x-2">
                                            @foreach($linkPage->blocks()->orderBy('order')->where('is_visible', true)->where('type', 'link')->get() as $block)
                                                @if($block->data['type'] === 'custom' && isset($block->data['icon']))
                                                    <img src="{{ $block->data['icon'] }}" alt="{{ $block->data['title'] }} icon" class="text-black size-5">
                                                @elseif($block->data['type'] === 'social' && isset($block->data['icon']))
                                                    <x-dynamic-component :component="'icons.' . $block->data['icon']" class="text-black dark:text-white size-5" />
                                                @endif
                                            @endforeach
                                        </div>
                                    </x-table.cell>
                                    <x-table.cell>
                                        <x-modal.trigger :name="'confirm-linkpage-deletion-' . $linkPage->id">
                                            <x-button
                                            variant="destructive"
                                            size="sm"
                                            >
                                                Delete <x-lucide-trash-2 class="ml-1 size-3" />
                                            </x-button>
                                        </x-modal.trigger>
                                    </x-table.cell>
                                </x-table.row>
                            @endforeach
                        </x-table.body>
                    </x-table>
                @endif
            </div>

            <x-button tag="a" href="{{ route('link-pages.create') }}">
                Create link page &rarr;
            </x-button>

            @foreach($linkPages as $linkPage)
                <x-modal :name="'confirm-linkpage-deletion-' . $linkPage->id">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900">
                            Are you sure you want to delete "{{ $linkPage->title }}"?
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            This action cannot be undone. All associated links will also be deleted.
                        </p>

                        <div class="flex justify-end mt-6 space-x-3">
                            <x-modal.close>
                                <x-button variant="secondary">
                                    Cancel
                                </x-button>
                            </x-modal.close>

                            <x-button
                                wire:click="deleteLinkPage({{ $linkPage->id }})"
                                variant="destructive"
                            >
                                Delete LinkPage
                            </x-button>
                        </div>
                    </div>
                </x-modal>
            @endforeach

            @unless(auth()->user()->subscribed('default'))
                <div class="mt-6">
                    <h2 class="text-lg font-semibold">Upgrade to Premium</h2>
                    <p class="mt-1 text-sm text-gray-600">Get access to more LinkPages and custom link cards.</p>
                    <form action="{{ route('subscription.create') }}" method="POST" class="mt-4">
                        @csrf
                        <x-button type="submit">Upgrade Now - $10/year</x-button>
                    </form>
                </div>
            @endunless
        </div>
    @endvolt
</x-layouts.app>
