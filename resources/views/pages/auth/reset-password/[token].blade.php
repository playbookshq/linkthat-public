<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use Masmerise\Toaster\Toaster;
use App\Services\SeoService;

use function Laravel\Folio\name;

name('password.reset');

new class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token, SeoService $seo): void
    {
        $seo->setTitle('Reset your Linkthat password')
            ->setDescription('Reset your Linkthat password.')
            ->setCanonical(route('password.reset', ['token' => $token]));

        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset($this->only('email', 'password', 'password_confirmation', 'token'), function ($user) {
            $user
                ->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])
                ->save();

            event(new PasswordReset($user));
        });

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));
            Toaster::error(__($status));
            return;
        }

        Toaster::success(__($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<x-layouts.auth>
    <x-card>
        @volt('pages.auth.reset-password')
            <form wire:submit="resetPassword" class="space-y-6">
                <div class="flex">
                    <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center space-x-2">
                        <x-logo class="size-4" />
                        <span class="font-bold">{{config('app.name')}}</span>
                    </a>
                </div>
                <div>
                    <x-heading size="lg">Reset your password</x-heading>
                    <x-subheading>Enter your new password</x-subheading>
                </div>

                <div class="space-y-6">
                    <x-input wire:model="email" label="Email" type="email" placeholder="Your email address" required
                        autofocus />
                    <x-input wire:model="password" label="New Password" type="password" placeholder="Your new password"
                        required />
                    <x-input wire:model="password_confirmation" label="Confirm Password" type="password"
                        placeholder="Confirm your new password" required />
                </div>

                <div class="space-y-2">
                    <x-button variant="default" class="w-full" type="submit">
                        {{ __('Reset Password') }}
                    </x-button>

                    <x-button variant="ghost" class="w-full" href="{{ route('login') }}" wire:navigate>
                        Back to login
                    </x-button>
                </div>
            </form>
        @endvolt
    </x-card>
</x-layouts.auth>
