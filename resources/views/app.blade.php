<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'BookFlow') }}</title>

        <meta name="description" content="BookFlow — Effortless booking management for service businesses. Publish your services, share your link, and accept paid bookings.">
        <meta name="theme-color" content="#6366f1">

        <!-- Favicons -->
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/png" href="/favicon-32.png" sizes="32x32">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Open Graph / social previews -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="BookFlow">
        <meta property="og:title" content="BookFlow — Effortless bookings for service businesses">
        <meta property="og:description" content="The Calendly alternative for small service businesses. Publish services, share a link, accept paid bookings.">
        <meta property="og:image" content="{{ url('/og-image.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="BookFlow — Effortless bookings for service businesses">
        <meta name="twitter:description" content="The Calendly alternative for small service businesses.">
        <meta name="twitter:image" content="{{ url('/og-image.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
