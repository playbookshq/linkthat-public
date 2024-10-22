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
                    <div class="space-y-4">
                        @foreach($linkPages as $linkPage)
                            <x-card>
                                <div>
                                    <h3 class="font-semibold">{{ $linkPage->title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $linkPage->username }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <x-button tag="a" href="{{ route('link-pages.edit', ['id' => $linkPage->id]) }}">
                                        Edit
                                    </x-button>
                                    <x-modal.trigger :name="'confirm-linkpage-deletion-' . $linkPage->id">
                                        <x-button variant="danger">
                                            Delete
                                        </x-button>
                                    </x-modal.trigger>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
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
        </div>
    @endvolt
</x-layouts.app>
