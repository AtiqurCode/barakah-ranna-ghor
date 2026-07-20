<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="barakah"
    data-theme="light"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script>
        window.applyBarakahTheme = function () {
            document.documentElement.setAttribute(
                'data-theme',
                localStorage.getItem('barakah-theme') || 'light'
            );
        };
        window.applyBarakahTheme();
    </script>

    <title>Checkout cancelled — {{ __('site.brand') }}</title>

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
        <section class="mx-auto max-w-[720px] px-6 py-16 text-center">
        <h1 class="font-newsreader text-4xl">Checkout cancelled</h1>
        <p class="mt-4 text-brand-muted">No payment was taken for {{ $order->product->translate('name') }}.</p>
        <a href="{{ route('product', $order->product) }}" class="btn-press mt-8 inline-flex rounded-[13px] bg-brand-accent px-6 py-3 font-semibold text-brand-accent-ink">Return to product</a>
        </section>
    </main>

    <x-storefront.footer />
    <x-storefront.floating-whatsapp />
    <livewire:product-chat />
</body>
</html>
