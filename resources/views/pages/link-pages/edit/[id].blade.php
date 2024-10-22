<?php

use function Laravel\Folio\{name, middleware};
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;

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
};

?>

<x-layouts.app>
    @volt('pages.link-pages.edit')
        <div>
            <h1 class="mb-4 text-2xl font-bold">Edit LinkPage: {{ $linkPage->username }}</h1>

            <x-form wire:submit="save">
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

                <x-button type="submit">Update LinkPage</x-button>
            </x-form>
        </div>
    @endvolt
</x-layouts.app>
