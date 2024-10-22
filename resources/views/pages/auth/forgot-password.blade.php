<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Masmerise\Toaster\Toaster;

use function Laravel\Folio\name;

name('password.request');

new class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink($this->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        Toaster::success(__($status));
    }
}; ?>

<x-layouts.auth>
    <x-card>
        @volt('pages.auth.forgot-password')
            <form wire:submit="sendPasswordResetLink" class="space-y-6">
                <div>
                    <x-heading size="lg">Reset your password</x-heading>
                    <x-subheading>Enter your email to receive a password reset link</x-subheading>
                </div>

                <div class="space-y-6">
                    <x-input wire:model="email" label="Email" type="email" placeholder="Your email address" required
                        autofocus />
                </div>

                <div class="space-y-2">
                    <x-button variant="default" class="w-full" type="submit">
                        {{ __('Email Password Reset Link') }}
                    </x-button>

                    <x-button variant="ghost" class="w-full" href="{{ route('login') }}" wire:navigate>
                        Back to login
                    </x-button>
                </div>
            </form>
        @endvolt
    </x-card>
</x-layouts.auth>
