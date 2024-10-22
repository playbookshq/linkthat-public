@props([
    'name' => null,
    'params' => [],
    'paramValues' => [],
    'quantity' => 'null',
])
<div
    {{ $attributes }}
    x-data="{
        params: @js($params),
        paramValues: @js($paramValues)
    }"
    x-on:click="
        let url = new URL(window.location);
        // Remove all existing query parameters
        url.search = '';
        params.forEach((param, index) => {
            if (paramValues[index] !== undefined) {
                url.searchParams.set(param, paramValues[index]);
            }
        });
        if ('{{ $quantity }}' !== 'null') {
            url.searchParams.set('quantity', '{{ $quantity }}');
        }
        window.history.pushState({}, '', url);
        $dispatch('modal-show', { name: '{{ $name }}' })
    "
>
    {{ $slot }}
</div>
