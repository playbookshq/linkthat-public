<?php

use function Laravel\Folio\{name, middleware};
use Livewire\Volt\Component;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;

name('dashboard');
middleware(['auth']);

new class extends Component {
    public $linkPages;

    public function mount(SeoService $seo): void
    {
        $this->linkPages = Auth::user()->linkPages;

        $seo->setTitle('Dashboard')
            ->setDescription('Manage your LinkPages.')
            ->setCanonical(route('dashboard'));
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
                                <div>
                                    <x-button tag="a" href="{{ route('link-pages.edit', ['id' => $linkPage->id]) }}">
                                        Edit
                                    </x-button>
                                </div>
                            </x-card>
                        @endforeach
                    </div>
                @endif
            </div>

            <x-button tag="a" href="{{ route('link-pages.create') }}">
                Create New LinkPage
            </x-button>
        </div>
    @endvolt
</x-layouts.app>
