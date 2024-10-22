<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;

name('link-pages.create');

new class extends Component {
    #[Rule('required|string|max:255|unique:link_pages,username')]
    public $username = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    #[Rule('required|string|in:light,dark')]
    public $theme = 'light';

    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Create LinkPage')
            ->setDescription('Create a new LinkPage for your profile.')
            ->setCanonical(route('link-pages.create'));
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            if (isset($e->validator->failed()['username']['Unique'])) {
                Toaster::error('This username is already taken. Please choose a different one.');
            }
            throw $e;
        }

        $user = Auth::user();

        if ($user->linkPages()->count() >= 2 && !$user->subscribed()) {
            Toaster::error('Free tier users are limited to 2 LinkPages.');
            return;
        }

        $linkPage = LinkPage::create([
            'user_id' => $user->id,
            'username' => $this->username,
            'title' => $this->title,
            'description' => $this->description,
            'theme' => $this->theme,
        ]);

        $this->reset(['username', 'title', 'description', 'theme']);

        Toaster::success('LinkPage created successfully!');
        $this->redirect(route('link-pages.edit', ['id' => $linkPage->id]), navigate: true);
    }
};

?>

<x-layouts.app>
    @volt('pages.link-pages.create')
        <div>
            <h1 class="mb-4 text-2xl font-bold">Create LinkPage</h1>

            <x-form wire:submit="save">
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

                <x-form.item>
                    <x-label for="theme">Theme</x-label>
                    <x-select wire:model="theme" id="theme">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </x-select>
                    <x-form.message for="theme" />
                </x-form.item>

                @error('limit')
                    <x-alert variant="error" class="mt-4">
                        {{ $message }}
                    </x-alert>
                @enderror

                <x-button type="submit">Create LinkPage</x-button>
            </x-form>
        </div>
    @endvolt
</x-layouts.app>
