<?php

use function Laravel\Folio\{name};
use Livewire\Volt\Component;
use Livewire\Attributes\Rule;
use App\Models\LinkPage;
use Illuminate\Support\Facades\Auth;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Support\Facades\Route;

name('link-pages.create');

new class extends Component {
    #[Rule('required|string|min:3|max:255|unique:link_pages,username')]
    public $username = '';

    #[Rule('required|string|max:255')]
    public $title = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    public $usernameStatus = '';

    protected $blacklistedUsernames = [
        'admin', 'administrator', 'mod', 'moderator', 'root', 'system',
        'sex', 'porn', 'xxx', 'nsfw', 'adult', 'fuck', 'shit', 'ass',
        'login', 'logout', 'signin', 'signout', 'register', 'password',
        'profile', 'settings', 'config', 'delete', 'remove', 'help',
        'support', 'contact', 'about', 'terms', 'privacy', 'legal',
        'official', 'staff', 'team', 'info', 'faq', 'news', 'blog',
        'forum', 'chat', 'mail', 'email', 'webmaster', 'postmaster',
        'hostmaster', 'abuse', 'security', 'billing', 'sales', 'jobs',
        'careers', 'api', 'dev', 'test', 'demo', 'beta', 'staging',
        'public', 'private', 'internal', 'external', 'corporate',
        // Add more blacklisted usernames as needed
    ];

    protected $reservedRoutes = [
        'home', 'login', 'register', 'logout', 'password', 'email', 'profile',
        'dashboard', 'settings', 'admin', 'about', 'contact', 'terms', 'privacy',
        'blog', 'posts', 'categories', 'tags', 'search', 'sitemap', 'rss',
        'api', 'webhook', 'callback', 'auth', 'oauth', 'social', 'account',
        'billing', 'payment', 'subscribe', 'unsubscribe', 'verify', 'confirm',
        // Add more reserved routes as needed
    ];

    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Create link page')
            ->setDescription('Create a new link page for your profile.')
            ->setCanonical(route('link-pages.create'));
    }

    public function updatedUsername()
    {
        $this->validateUsername();
    }

    public function validateUsername()
    {
        $this->usernameStatus = '';

        if (empty($this->username)) {
            return;
        }

        if (strlen($this->username) < 3) {
            $this->usernameStatus = 'Username must be at least 3 characters long.';
            return;
        }

        if (in_array(strtolower($this->username), $this->blacklistedUsernames)) {
            $this->usernameStatus = 'This username is not allowed.';
            return;
        }

        if (in_array(strtolower($this->username), $this->reservedRoutes)) {
            $this->usernameStatus = 'This username conflicts with an existing route.';
            return;
        }

        if (Route::has($this->username)) {
            $this->usernameStatus = 'This username conflicts with an existing route.';
            return;
        }

        $validator = Validator::make(
            ['username' => $this->username],
            ['username' => ['required', 'string', 'max:255', ValidationRule::unique('link_pages', 'username')]]
        );

        if ($validator->fails()) {
            $this->usernameStatus = $validator->errors()->first('username');
        } else {
            $this->usernameStatus = 'Username is available!';
        }
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        if (Gate::denies('create-link-page')) {
            Toaster::error('You have reached the maximum number of LinkPages for your plan.');
            return;
        }

        if (in_array(strtolower($this->username), $this->blacklistedUsernames) ||
            in_array(strtolower($this->username), $this->reservedRoutes) ||
            Route::has($this->username)) {
            Toaster::error('This username is not allowed.');
            return;
        }

        $linkPage = LinkPage::create([
            'user_id' => $user->id,
            'username' => $this->username,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->reset(['username', 'title', 'description', 'usernameStatus']);

        Toaster::success('Link page created successfully!');
        $this->redirect(route('link-pages.edit', ['id' => $linkPage->id]), navigate: true);
    }
};

?>

<x-layouts.app>
    @volt('pages.link-pages.create')
        <div class="flex flex-col items-center justify-center p-4 bg-gradient-to-b">
            <div class="w-full max-w-md">
                <x-card>
                    <x-card.header>
                        <x-card.title>Create link page</x-card.title>
                        <x-card.description>Create a new link page for your profile.</x-card.description>
                    </x-card.header>

                    <x-card.content>
                        <x-form wire:submit="save" class="space-y-4">
                            <x-form.item>
                                <x-label for="username">Username</x-label>
                                <x-input wire:model.live="username" id="username" type="text" />
                                <x-form.message for="username" />
                                @if ($usernameStatus && !empty($username))
                                    <p class="{{ $usernameStatus === 'Username is available!' ? 'text-green-600' : 'text-red-600' }} text-sm mt-1">
                                        {{ $usernameStatus }}
                                    </p>
                                @endif
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
