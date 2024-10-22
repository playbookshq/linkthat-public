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
                    throw new \Exception('You are not authorized to delete this LinkPage.');
                }

                // Delete associated links
                $linkPage->links()->delete();

                // Delete the LinkPage
                $linkPage->delete();

                $this->linkPages = Auth::user()->linkPages;
                $this->linkPageToDelete = null;

                Toaster::success('LinkPage deleted successfully.');
            } catch (\Exception $e) {
                Toaster::error('Error deleting LinkPage: ' . $e->getMessage());
            }
        }
    }
};

?>

<x-layouts.app>
    @volt('pages.dashboard')
        <div>
            <h1 class="mb-4 text-2xl font-bold">Dashboard</h1>
            <div class="mb-6">
                <h2 class="mb-2 text-xl font-semibold">Your LinkPages</h2>
                @if($linkPages->isEmpty())
                    <p>You haven't created any LinkPages yet.</p>
                @else
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
                                        <a href="{{ route('link-pages.edit', ['id' => $linkPage->id]) }}" class="text-blue-600 hover:underline"
                                        wire:navigate
                                            >
                                            {{ $linkPage->title }}
                                        </a>
                                    </x-table.cell>
                                    <x-table.cell>{{ $linkPage->username }}</x-table.cell>
                                    <x-table.cell>
                                        <div class="flex space-x-2">
                                            @foreach($linkPage->links()->orderBy('order')->where('is_visible', true)->get() as $link)
                                                @if($link->type === 'custom' && $link->icon)
                                                    <img src="{{ $link->icon }}" alt="{{ $link->title }} icon" class="text-black size-5">
                                                @elseif($link->type === 'social' && $link->icon)
                                                    <x-dynamic-component :component="'icons.' . $link->icon" class="text-black size-5" />
                                                @endif
                                            @endforeach
                                        </div>
                                    </x-table.cell>
                                    <x-table.cell>
                                        <x-modal.trigger :name="'confirm-linkpage-deletion-' . $linkPage->id">
                                            <x-button variant="danger">
                                                Delete
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
                Create New LinkPage
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
