<?php
use function Laravel\Folio\name;

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

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
};
?>

<x-layouts.auth>
    @volt('pages.auth.login')
        <div> <!-- Add this wrapper div -->
            <x-card>
                <form wire:submit='login' class="space-y-6">
                    <div>
                        <x-heading size="lg">Log in to your account</x-heading>
                        <x-subheading>Welcome back!</x-subheading>
                    </div>

                    <div class="space-y-6">
                        <x-input wire:model='form.email' label="Email" type="email" placeholder="Your email address" />

                        <x-form.label class="flex justify-between">
                                Password

                                <x-button variant="link" href="{{ route('password.request') }}" wire:navigate variant="subtle">Forgot password?
                            </x-button>
                        </x-form.label>

                        <x-input wire:model='form.password' type="password" placeholder="Your password" />

                        <x-checkbox wire:model="form.remember" label="Remember me" />
                    </div>

                    <div class="space-y-2">
                        <x-button variant="default" class="w-full" type="submit">Log in</x-button>

                        <x-button variant="ghost" class="w-full" href="{{ route('register') }}" wire:navigate>Sign up for a
                            new
                            account
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div> <!-- Close the wrapper div -->
    @endvolt
</x-layouts.auth>
