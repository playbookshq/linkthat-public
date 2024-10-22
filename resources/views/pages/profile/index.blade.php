<?php

use App\Models\User;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;
use App\Services\SeoService;
use Masmerise\Toaster\Toaster;

use function Laravel\Folio\{middleware, name};

middleware(['auth']);

name('profile.update');

new class extends Component {
    public string $name = '';
    public string $email = '';

    // Update Password Properties
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Delete User Property
    public string $delete_password = '';

    public function mount(SeoService $seo): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;

        $seo->setTitle('Update Profile')
            ->setDescription('Update your account profile information and email address.')
            ->setCanonical(route('profile.update'))
            ->setOgImage(asset('images/profile-og.jpg'));
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Toaster::success('Profile updated successfully.');
    }

    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Toaster::success('Verification link sent!');
    }

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Toaster::success('Password updated successfully.');
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'delete_password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        Toaster::success('Account deleted successfully.');

        $this->redirect('/', navigate: true);
    }
}; ?>
<x-layouts.app>
    @volt('pages.profile.update')
        <div class="space-y-6">
            <x-card>
                <form wire:submit="updateProfileInformation" class="space-y-6">
                    <div>
                        <x-heading size="lg">Profile Information</x-heading>
                        <x-subheading>Update your account's profile information and email address.</x-subheading>
                    </div>

                    <div class="space-y-6">
                        <x-input wire:model="name" label="Name" type="text" placeholder="Your name" required
                            autofocus />

                        <x-input wire:model="email" label="Email" type="email" placeholder="Your email address"
                            required />

                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                            <div>
                                <p class="text-sm text-gray-800">
                                    Your email address is unverified.

                                    <x-button wire:click.prevent="sendVerification" variant="link">
                                        Click here to re-send the verification email.
                                    </x-button>
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <x-button type="submit" variant="default">Save</x-button>
                    </div>
                </form>
            </x-card>

            <x-card>
                <form wire:submit="updatePassword" class="space-y-6">
                    <div>
                        <x-heading size="lg">Update Password</x-heading>
                        <x-subheading>Ensure your account is using a long, random password to stay secure.
                        </x-subheading>
                    </div>

                    <div class="space-y-6">
                        <x-input wire:model="current_password" label="Current Password" type="password" required />
                        <x-input wire:model="password" label="New Password" type="password" required />
                        <x-input wire:model="password_confirmation" label="Confirm Password" type="password" required />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <x-button type="submit" variant="default">Update Password</x-button>
                    </div>
                </form>
            </x-card>
            <x-card>
                <div class="space-y-6">
                    <div>
                        <x-heading size="lg">Delete Account</x-heading>
                        <x-subheading>Once your account is deleted, all of its resources and data will be permanently
                            deleted.</x-subheading>
                    </div>

                    <x-modal.trigger name="delete-profile">
                        <x-button variant="destructive" class="mt-4">Delete Account</x-button>
                    </x-modal.trigger>

                    <x-modal name="delete-profile" maxWidth="md" class="space-y-6">
                        <form wire:submit="deleteUser">
                            <div>
                                <x-heading size="lg">Are you sure you want to delete your account?</x-heading>

                                <x-subheading>
                                    Once your account is deleted, all of its resources and data will be permanently
                                    deleted.
                                </x-subheading>
                            </div>

                            <div class="mt-6">
                                <x-input wire:model="delete_password" label="Password" type="password"
                                    placeholder="Password" required />
                            </div>

                            <div class="flex items-end justify-end gap-2 mt-6">
                                <x-modal.close>
                                    <x-button variant="ghost">Cancel</x-button>
                                </x-modal.close>

                                <x-button type="submit" variant="destructive">Delete Account</x-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </x-card>
        </div>
    @endvolt
</x-layouts.app>
