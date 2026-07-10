<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="barakah"
    data-theme="light"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Apply the saved theme before first paint, and re-apply after every
         Livewire (wire:navigate) page swap so dark mode persists across routes. --}}
    <script>
        window.applyBarakahTheme = function () {
            document.documentElement.setAttribute(
                'data-theme',
                localStorage.getItem('barakah-theme') || 'light'
            );
        };
        window.applyBarakahTheme();
        document.addEventListener('livewire:navigated', window.applyBarakahTheme);
    </script>

    @php
        $pageTitle = match (true) {
            request()->routeIs('products') => __('site.nav.products'),
            request()->routeIs('about') => __('site.nav.about'),
            request()->routeIs('contact') => __('site.nav.contact'),
            request()->routeIs('product') => request()->route('product')?->translate('name'),
            default => null,
        };
    @endphp
    <title>{{ $pageTitle ? $pageTitle . ' — ' . __('site.brand') : __('site.brand') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&family=Hanken+Grotesk:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-brand-bg font-hanken text-brand-text antialiased transition-colors">
    <x-storefront.header />

    <main class="animate-fade-up">
        {{ $slot }}
    </main>

    <x-storefront.footer />

    <x-storefront.floating-whatsapp />
</body>
</html>
