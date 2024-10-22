<?php

use function Laravel\Folio\{name, middleware};
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Gate;

name('link-pages.edit');
middleware(['auth']);

new class extends Component {
    public ?LinkPage $linkPage = null;

    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    #[Rule('required|string|in:light,dark')]
    public $theme = 'light';

    public function mount($id, SeoService $seo): void
    {
        $this->linkPage = Auth::user()->linkPages()->findOrFail($id);

        $this->title = $this->linkPage->title;
        $this->description = $this->linkPage->description;
        $this->theme = $this->linkPage->theme;

        $seo->setTitle("Edit LinkPage: {$this->linkPage->username}")
            ->setDescription('Edit your LinkPage settings.');
    }

    public function save()
    {
        $this->validate();

        $this->linkPage->update([
            'title' => $this->title,
            'description' => $this->description,
            'theme' => $this->theme,
        ]);

        Toaster::success('LinkPage updated successfully!');
    }

    public function getPublicProfileUrlProperty()
    {
        return route('public-link-page', ['username' => $this->linkPage->username]);
    }

    public function getDashboardUrlProperty()
    {
        return route('dashboard');
    }

    public function getCanUseCustomLinksProperty()
    {
        return Gate::allows('use-custom-link-cards');
    }
};

?>

<x-layouts.app>
    @volt('pages.link-pages.edit')
        <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gradient-to-b">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold">Edit LinkPage: {{ $linkPage->username }}</h1>
                    <div class="space-x-4">
                        <a href="{{ $this->dashboardUrl }}" wire:navigate class="text-sm text-gray-500 transition-colors duration-200 hover:text-gray-700">
                            Back to Dashboard
                        </a>
                        <a href="{{ $this->publicProfileUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm text-gray-500 transition-colors duration-200 hover:text-gray-700">
                            View Public Profile
                        </a>
                    </div>
                </div>

                <x-form wire:submit="save">
                    <x-card>
                        <x-card.content>
                            <x-form.item>
                                <x-label for="title">Title</x-label>
                                <x-input wire:model="title" id="title" type="text" />
                                <x-form.message for="title" />
                            </x-form.item>

                            <x-form.item>
                                <x-label for="description">Description</x-label>
                                <x-textarea wire:model="description" id="description" />
                                <x-form.message for="description" />
                            </x-form.item>

                            <x-form.item>
                                <x-label for="theme">Theme</x-label>
                                <x-select wire:model="theme" id="theme">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                </x-select>
                                <x-form.message for="theme" />
                            </x-form.item>

                            <x-button type="submit" class="w-full">Update LinkPage</x-button>
                        </x-card.content>
                    </x-card>
                </x-form>

                <div class="mt-8">
                    <livewire:link-manager :link-page="$linkPage" />
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.app>
