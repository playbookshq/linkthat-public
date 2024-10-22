<?php
use function Laravel\Folio\name;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Services\SeoService;

name('register');

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Register an account on Linkthat')
            ->setDescription('Register an account on Linkthat.')
            ->setCanonical(route('register'));
    }
};
?>

<x-layouts.auth>
    @volt('pages.auth.register')
        <x-card class="space-y-6">
            <div class="flex">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center space-x-2">
                    <x-logo class="size-4" />
                    <span class="font-bold">{{config('app.name')}}</span>
                </a>
            </div>
            <form wire:submit='register' class="space-y-6">
                <div>
                    <x-heading size="lg">Register a new account</x-heading>
                    <x-subheading>Lets's get started</x-subheading>
                </div>

                <div class="space-y-6">
                    <x-input label="Name" type="text" placeholder="Your name" wire:model='name' />

                    <x-input label="Email" type="email" placeholder="Your email address" wire:model='email' />

                    <x-input label="Password" type="password" placeholder="Your password" wire:model='password' />

                    <x-input label="Confirm Password" type="password" placeholder="Confirm your password"
                        wire:model='password_confirmation' />
                </div>

                <div class="space-y-2">
                    <x-button variant="default" class="w-full" type="submit">Register</x-button>

                    <x-button variant="ghost" class="w-full" href="{{ route('login') }}" wire:navigate>Already have an
                        account?
                    </x-button>
                </div>
            </form>
        </x-card>
    @endvolt
</x-layouts.auth>
