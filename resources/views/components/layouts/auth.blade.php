<?php
use App\Services\SeoService;

$seo = app(SeoService::class);
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seo->getTitle() }}</title>
    <meta name="description" content="{{ $seo->getDescription() }}">

    @if($seo->getCanonical())
        <link rel="canonical" href="{{ $seo->getCanonical() }}">
    @endif

    @if($seo->getOgImage())
        <meta property="og:image" content="{{ $seo->getOgImage() }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="flex items-center justify-center max-w-md min-h-screen m-auto bg-white">
    <div class="w-full">
        {{ $slot }}
    </div>
    @persist('toast')
        <x-toaster-hub />
    @endpersist
</body>

</html>
