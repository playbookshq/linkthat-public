<?php
use function Laravel\Folio\{middleware, name};
use Livewire\Volt\Component;
use App\Services\SeoService;

middleware(['auth', 'verified']);
name('playground');

new class extends Component {
    public function mount(SeoService $seo): void
    {
        $seo->setTitle('Playground')
            ->setDescription('This is the playground page.')
            ->setCanonical(route('playground'))
            ->setOgImage(asset('images/playground-og.jpg'));
    }
};
?>

<x-layouts.app>
    @volt('pages.playground')
        <div>
            <x-button>Hi everyone</x-button>
        </div>
    @endvolt
</x-layouts.app>
