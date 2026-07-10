@php
    $nav = [
        ['label' => __('site.nav.home'), 'route' => 'home', 'active' => request()->routeIs('home')],
        ['label' => __('site.nav.products'), 'route' => 'products', 'active' => request()->routeIs('products', 'product')],
        ['label' => __('site.nav.about'), 'route' => 'about', 'active' => request()->routeIs('about')],
        ['label' => __('site.nav.contact'), 'route' => 'contact', 'active' => request()->routeIs('contact')],
    ];

    $locale = app()->getLocale();
    $targetLocale = $locale === 'en' ? 'bn' : 'en';
    $langLabel = $locale === 'en' ? 'BN' : 'EN';
@endphp

<header
    x-data="{ mobileOpen: false }"
    class="sticky top-0 z-[60] border-b border-brand-border backdrop-blur-lg"
    style="background: color-mix(in srgb, var(--brand-bg) 85%, transparent)"
>
    <div class="mx-auto flex h-[70px] max-w-[1180px] items-center justify-between gap-4 px-6">
        {{-- Brand --}}
        <a href="{{ route('home') }}" wire:navigate class="flex min-w-0 items-center gap-[11px]">
            <x-brand-logo class="size-9 flex-none" />
            {{-- Brand name is hidden on mobile; the logo alone represents the brand there. --}}
            <span class="hidden min-w-0 flex-col items-start leading-none sm:flex">
                <span class="truncate text-base font-semibold -tracking-[.01em]">{{ __('site.brand') }}</span>
                <span class="mt-1 hidden truncate text-[9.5px] font-semibold uppercase tracking-[.16em] text-brand-muted md:block">{{ __('site.brand_sub') }}</span>
            </span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-0.5 lg:flex">
            @foreach ($nav as $item)
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    class="relative px-[13px] py-[9px] text-[14.5px] font-medium transition-colors hover:text-brand-accent {{ $item['active'] ? 'text-brand-accent' : 'text-brand-text' }}"
                >
                    {{ $item['label'] }}
                    <span class="absolute inset-x-[13px] bottom-[3px] h-0.5 rounded-sm bg-brand-accent transition-opacity {{ $item['active'] ? 'opacity-100' : 'opacity-0' }}"></span>
                </a>
            @endforeach
        </nav>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            {{-- Mobile menu toggle --}}
            <button
                @click="mobileOpen = !mobileOpen"
                title="Menu"
                class="btn-tap grid size-[38px] place-items-center rounded-[10px] border border-brand-border bg-brand-surface text-brand-text hover:border-brand-accent hover:text-brand-accent lg:hidden"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>

            {{-- Language toggle --}}
            <a
                href="{{ route('language.switch', $targetLocale) }}"
                title="Switch language"
                class="btn-tap flex h-[38px] items-center rounded-[10px] border border-brand-border bg-brand-surface px-[13px] text-[13px] font-semibold text-brand-text hover:border-brand-accent hover:text-brand-accent"
            >{{ $langLabel }}</a>

            {{-- Theme toggle --}}
            <button
                @click="
                    const el = document.documentElement;
                    const next = el.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    el.setAttribute('data-theme', next);
                    localStorage.setItem('barakah-theme', next);
                "
                title="Toggle theme"
                class="btn-tap grid size-[38px] place-items-center rounded-[10px] border border-brand-border bg-brand-surface text-brand-text hover:border-brand-accent hover:text-brand-accent"
            >
                <svg class="theme-icon-sun" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" /></svg>
                <svg class="theme-icon-moon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" /></svg>
            </button>

            {{-- WhatsApp CTA --}}
            <a
                href="{{ \App\Support\WhatsApp::greeting() }}"
                target="_blank"
                rel="noopener"
                class="btn-press btn-glow inline-flex h-[38px] items-center gap-2 rounded-[10px] bg-brand-accent px-4 text-[13px] font-semibold text-brand-accent-ink"
            >
                <x-icon.whatsapp class="size-4" />
                <span class="hidden sm:inline">{{ __('site.order_short') }}</span>
            </a>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div
        x-show="mobileOpen"
        x-collapse
        x-cloak
        class="border-t border-brand-border bg-brand-surface px-4 pb-3.5 pt-2 lg:hidden"
    >
        <div class="flex flex-col gap-0.5">
            @foreach ($nav as $item)
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    @click="mobileOpen = false"
                    class="flex items-center justify-between gap-2.5 rounded-[10px] px-3.5 py-3 text-base font-semibold {{ $item['active'] ? 'bg-brand-accent-soft text-brand-accent' : 'text-brand-text' }}"
                >
                    {{ $item['label'] }}
                    <span class="size-[7px] rounded-full bg-brand-accent {{ $item['active'] ? 'opacity-100' : 'opacity-0' }}"></span>
                </a>
            @endforeach
        </div>
    </div>
</header>
