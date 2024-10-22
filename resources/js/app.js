import './bootstrap';
import collapse from "@alpinejs/collapse";
import anchor from "@alpinejs/anchor";
import '../../vendor/masmerise/livewire-toaster/resources/js';
import '@wotz/livewire-sortablejs';

// Add dark mode detection
function setDarkMode() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Set initial dark mode
setDarkMode();

// Listen for changes in system color scheme
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', setDarkMode);

document.addEventListener(
    "alpine:init",
    () => {
        const modules = import.meta.glob("./plugins/**/*.js", { eager: true });

        for (const path in modules) {
            window.Alpine.plugin(modules[path].default);
        }
        window.Alpine.plugin(collapse);
        window.Alpine.plugin(anchor);
    },
    { once: true },
);
