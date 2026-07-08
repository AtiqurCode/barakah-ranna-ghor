@php
    $nav = [
        ['label' => __('site.nav.home'), 'route' => 'home'],
        ['label' => __('site.nav.products'), 'route' => 'products'],
        ['label' => __('site.nav.about'), 'route' => 'about'],
        ['label' => __('site.nav.contact'), 'route' => 'contact'],
    ];

    $footerProducts = \App\Models\Product::ordered()->take(4)->get();
@endphp

<footer class="border-t border-brand-border bg-brand-surface">
    <div class="mx-auto grid max-w-[1180px] grid-cols-1 gap-10 px-6 pb-7 pt-14 md:grid-cols-2 lg:grid-cols-[1.4fr_.7fr_.7fr_1.2fr]">
        {{-- Brand + socials --}}
        <div>
            <div class="mb-3.5 flex items-center gap-[11px]">
                <span class="grid size-9 place-items-center rounded-[10px] bg-brand-accent text-lg font-bold text-brand-accent-ink" style="font-family: 'Hind Siliguri', serif">ব</span>
                <span class="text-base font-semibold">{{ __('site.brand') }}</span>
            </div>
            <p class="mb-[18px] max-w-[280px] text-sm leading-relaxed text-brand-muted">{{ __('site.f_tagline') }}</p>
            <div class="flex gap-2.5">
                <a href="{{ \App\Support\WhatsApp::greeting() }}" target="_blank" rel="noopener" title="WhatsApp" class="grid size-[38px] place-items-center rounded-[10px] border border-brand-border text-brand-text transition hover:border-brand-accent hover:text-brand-accent">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm0 18.15h-.01a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.11.82.83-3.04-.2-.31a8.22 8.22 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.19 8.19 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.25 8.24Zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43l-.48-.01c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.22-.17-.47-.29Z" /></svg>
                </a>
                <a href="{{ config('barakah.social.facebook') }}" target="_blank" rel="noopener" title="Facebook" class="grid size-[38px] place-items-center rounded-[10px] border border-brand-border text-[15px] font-bold text-brand-text transition hover:border-brand-accent hover:text-brand-accent">f</a>
                <a href="{{ config('barakah.social.instagram') }}" target="_blank" rel="noopener" title="Instagram" class="grid size-[38px] place-items-center rounded-[10px] border border-brand-border text-brand-text transition hover:border-brand-accent hover:text-brand-accent">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="3.6" /><circle cx="17.4" cy="6.6" r="1" fill="currentColor" stroke="none" /></svg>
                </a>
            </div>
        </div>

        {{-- Explore --}}
        <div>
            <h4 class="mb-3.5 text-[13px] font-semibold uppercase tracking-[.04em] text-brand-muted">{{ __('site.f_col_explore') }}</h4>
            <div class="flex flex-col gap-[11px]">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}" wire:navigate class="text-left text-[14.5px] text-brand-text transition-colors hover:text-brand-accent">{{ $item['label'] }}</a>
                @endforeach
            </div>
        </div>

        {{-- Products --}}
        <div>
            <h4 class="mb-3.5 text-[13px] font-semibold uppercase tracking-[.04em] text-brand-muted">{{ __('site.f_col_shop') }}</h4>
            <div class="flex flex-col gap-[11px]">
                @foreach ($footerProducts as $product)
                    <a href="{{ route('product', $product) }}" wire:navigate class="text-left text-[14.5px] text-brand-text transition-colors hover:text-brand-accent">{{ $product->translate('name') }}</a>
                @endforeach
            </div>
        </div>

        {{-- Newsletter --}}
        <div>
            <h4 class="mb-3.5 text-[13px] font-semibold uppercase tracking-[.04em] text-brand-muted">{{ __('site.f_news_title') }}</h4>
            <p class="mb-3.5 text-sm leading-snug text-brand-muted">{{ __('site.f_news_sub') }}</p>
            <livewire:newsletter />
        </div>
    </div>

    <div class="border-t border-brand-border">
        <div class="mx-auto flex max-w-[1180px] flex-wrap items-center justify-between gap-3 px-6 py-5">
            <span class="text-[13px] text-brand-muted">{{ __('site.f_bottom') }}</span>
            <span class="text-[13px] text-brand-muted">{{ __('site.f_made') }}</span>
        </div>
    </div>
</footer>
