<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Services\SeoService;

middleware(['auth', 'verified']);
name('dashboard');

new class extends Component {
    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Dashboard')
            ->setDescription('Manage your link pages and account.')
            ->setCanonical(route('dashboard'));
    }
};
?>

<x-layouts.app>
    @volt('pages.dashboard')
        <div>
            <h1 class="mb-4 text-2xl font-bold">Dashboard</h1>
            <!-- Add dashboard content here -->
        </div>
    @endvolt
</x-layouts.app>
