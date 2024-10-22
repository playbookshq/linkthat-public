<?php
use function Laravel\Folio\name;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Masmerise\Toaster\Toaster;
use App\Services\SeoService;

name('login');

new class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Login to Linkthat')
            ->setDescription('Login to your Linkthat account.')
            ->setCanonical(route('login'));
    }
};
?>

<x-layouts.auth>
    @volt('pages.auth.login')
        <div>
            <x-card class="space-y-6">
                <div class="flex">
                    <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center space-x-2">
                        <x-logo class="size-4" />
                        <span class="font-bold">{{config('app.name')}}</span>
                    </a>
                </div>
                <form wire:submit='login' class="space-y-4">
                    <div>
                        <x-heading size="lg">Log in to your account</x-heading>
                        <x-subheading>Welcome back!</x-subheading>
                    </div>

                    <div class="space-y-6">
                        <x-form.label class="flex justify-between">
                            <span>Email</span>
                        </x-form.label>

                        <x-input wire:model='form.email' type="email" placeholder="Your email address" tabindex="1" />

                        <x-form.label class="flex justify-between">
                            <span>Password</span>

                            <a href="{{ route('password.request') }}" wire:navigate class="underline" tabindex="4">Forgot password?</a>
                        </x-form.label>

                        <x-input wire:model='form.password' type="password" placeholder="Your password" tabindex="2" />

                        <x-checkbox wire:model="form.remember" label="Remember me" tabindex="3" />
                    </div>

                    <div class="space-y-2">
                        <x-button variant="default" class="w-full" type="submit" tabindex="5">Log in</x-button>

                        <x-button variant="ghost" class="w-full" href="{{ route('register') }}" wire:navigate tabindex="6">Sign up for a new account</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    @endvolt
</x-layouts.auth>
