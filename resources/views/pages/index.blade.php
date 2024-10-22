<?php
use function Laravel\Folio\{name};
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

name('home');

new class extends Component {
    // dashboard logic
};
?>

<x-layouts.app>
    @volt('pages.home')
    <div>
        <x-card class="space-y-6">
                <x-heading size="lg">Welcome to home</x-heading>
                <x-subheading>Let's get started in the <x-link href="/playground">playground.</x-link>
                </x-subheading>
        </x-card>
    </div>
    @endvolt
</x-layouts.app>
