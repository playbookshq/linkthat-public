<?php

use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Services\SeoService;

middleware(['auth', 'verified']);
name('subscription');

new class extends Component {
    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Subscription')
            ->setDescription('Manage your subscription plan.')
            ->setCanonical(route('subscription'));
    }
};
?>

<x-layouts.app>
    @volt('pages.subscription')
        <div>
            <h1 class="mb-4 text-2xl font-bold">Subscription</h1>
            <!-- Add subscription management content here -->
        </div>
    @endvolt
</x-layouts.app>
