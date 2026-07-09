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
                <x-brand-logo class="size-9" />
                <span class="text-base font-semibold">{{ __('site.brand') }}</span>
            </div>
            <p class="mb-[18px] max-w-[280px] text-sm leading-relaxed text-brand-muted">{{ __('site.f_tagline') }}</p>
            <div class="flex gap-2.5">
                <a href="{{ \App\Support\WhatsApp::greeting() }}" target="_blank" rel="noopener" title="WhatsApp" class="grid size-[38px] place-items-center rounded-[10px] border border-brand-border text-brand-text btn-tap hover:border-brand-accent hover:text-brand-accent">
                    <x-icon.whatsapp class="size-[18px]" />
                </a>
                <a href="{{ config('barakah.social.facebook') }}" target="_blank" rel="noopener" title="Facebook" class="grid size-[38px] place-items-center rounded-[10px] border border-brand-border text-[15px] font-bold text-brand-text btn-tap hover:border-brand-accent hover:text-brand-accent">f</a>
                <a href="{{ config('barakah.social.instagram') }}" target="_blank" rel="noopener" title="Instagram" class="grid size-[38px] place-items-center rounded-[10px] border border-brand-border text-brand-text btn-tap hover:border-brand-accent hover:text-brand-accent">
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
