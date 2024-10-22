<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Gate;

name('link-pages.create');

new class extends Component {
    #[Rule('required|string|max:255|unique:link_pages,username')]
    public $username = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Create LinkPage')
            ->setDescription('Create a new LinkPage for your profile.')
            ->setCanonical(route('link-pages.create'));
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        if (Gate::denies('create-link-page')) {
            Toaster::error('You have reached the maximum number of LinkPages for your plan.');
            return;
        }

        $linkPage = LinkPage::create([
            'user_id' => $user->id,
            'username' => $this->username,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->reset(['username', 'title', 'description']);

        Toaster::success('LinkPage created successfully!');
        $this->redirect(route('link-pages.edit', ['id' => $linkPage->id]), navigate: true);
    }
};

?>

<x-layouts.app>
    @volt('pages.link-pages.create')
        <div class="flex flex-col items-center justify-center min-h-screen p-4 bg-gradient-to-b">
            <div class="w-full max-w-md">
                <x-card>
                    <x-card.header>
                        <x-card.title>Create LinkPage</x-card.title>
                        <x-card.description>Create a new LinkPage for your profile.</x-card.description>
                    </x-card.header>

                    <x-card.content>
                        <x-form wire:submit="save" class="space-y-4">
                            <x-form.item>
                                <x-label for="username">Username</x-label>
                                <x-input wire:model="username" id="username" type="text" />
                                <x-form.message for="username" />
                            </x-form.item>

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

                            @error('limit')
                                <x-alert variant="error" class="mt-4">
                                    {{ $message }}
                                </x-alert>
                            @enderror

                            <x-button type="submit" class="w-full">Create LinkPage</x-button>
                        </x-form>
                    </x-card.content>
                </x-card>
            </div>
        </div>
    @endvolt
</x-layouts.app>
