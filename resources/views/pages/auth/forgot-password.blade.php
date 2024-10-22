<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Masmerise\Toaster\Toaster;
use App\Services\SeoService;

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
            Toaster::error(__($status));
            return;
        }

        $this->reset('email');

        Toaster::success(__($status));
    }

    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Reset your Linkthat password')
            ->setDescription('Reset your Linkthat password.')
            ->setCanonical(route('password.request'));
    }
}; ?>

<x-layouts.auth>
    @volt('pages.auth.forgot-password')
    <x-card class="space-y-6">
            <div class="flex">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center space-x-2">
                    <x-logo class="size-4" />
                    <span class="font-bold">{{config('app.name')}}</span>
                </a>
            </div>
            <form wire:submit.prevent="sendPasswordResetLink" class="space-y-6">
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
                        {{ __('Send password reset link') }}
                    </x-button>

                    <x-button variant="ghost" class="w-full" href="{{ route('login') }}" wire:navigate>
                        Back to login
                    </x-button>
                </div>
            </form>
        </x-card>
    @endvolt
</x-layouts.auth>
