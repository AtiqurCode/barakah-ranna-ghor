<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.storefront')] class extends Component
{
    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $featured = Product::featured()->ordered()->get();

        return [
            'featured' => $featured,
            'slides' => $featured->map(fn (Product $product): array => [
                'name' => $product->translate('name'),
                'image' => $product->image_url,
                'url' => route('product', $product),
            ])->values()->all(),
        ];
    }
};
?>

<div>
    {{-- Hero: static content + image-only slider --}}
    <section
            x-data="{
                slides: @js($slides),
                current: 0,
                timer: null,
                start() { this.stop(); if (this.slides.length > 1) this.timer = setInterval(() => this.next(), 6000); },
                stop() { if (this.timer) { clearInterval(this.timer); this.timer = null; } },
                next() { this.current = (this.current + 1) % this.slides.length; },
                prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length; },
                go(i) { this.current = i; this.start(); },
            }"
            x-init="start()"
            @mouseenter="stop()"
            @mouseleave="start()"
        >
            <div class="mx-auto grid max-w-[1180px] grid-cols-1 items-center gap-10 px-6 pb-11 pt-12 sm:pt-[76px] lg:grid-cols-[1.05fr_.95fr] lg:gap-14">
                {{-- Static hero content --}}
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-brand-accent-soft px-3.5 py-[7px] text-[12.5px] font-semibold text-brand-accent">
                        <span class="size-1.5 rounded-full bg-brand-accent"></span>{{ __('site.hero_badge') }}
                    </span>
                    <h1 class="mt-[22px] font-newsreader text-[34px] font-normal leading-[1.06] -tracking-[.01em] text-balance sm:text-[46px] lg:text-[60px] lg:leading-[1.04]">{{ __('site.hero_title') }}</h1>
                    <p class="mt-[22px] max-w-[460px] text-[15.5px] leading-relaxed text-brand-muted text-pretty sm:text-[17px]">{{ __('site.hero_sub') }}</p>

                    <div class="mt-[30px] flex flex-wrap gap-3">
                        <a href="{{ route('products') }}" wire:navigate class="btn-press btn-glow flex h-[50px] items-center rounded-xl bg-brand-accent px-[26px] text-[15px] font-semibold text-brand-accent-ink">{{ __('site.hero_cta1') }}</a>
                        <a href="{{ route('about') }}" wire:navigate class="btn-press flex h-[50px] items-center rounded-xl border border-brand-border bg-brand-surface px-[26px] text-[15px] font-semibold text-brand-text hover:border-brand-accent hover:text-brand-accent">{{ __('site.hero_cta2') }} →</a>
                    </div>

                    <div class="mt-[34px] flex flex-wrap gap-2.5">
                        @foreach (__('site.trust') as $chip)
                            <span class="inline-flex items-center gap-[7px] text-[13px] font-medium text-brand-muted">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--brand-accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>{{ $chip }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Image-only slider (click → product detail) --}}
                @if (! empty($slides))
                <div class="relative">
                    <a :href="slides[current].url" wire:navigate :aria-label="slides[current].name"
                        class="group relative block aspect-[4/5] overflow-hidden rounded-[22px] border border-brand-border bg-brand-s2 shadow-[0_40px_80px_-50px_rgba(0,0,0,.4)]">
                        <template x-for="(slide, i) in slides" :key="i">
                            <img
                                :src="slide.image" :alt="slide.name" draggable="false"
                                x-show="current === i"
                                x-transition:enter="transition-opacity duration-500 ease-out"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition-opacity duration-500 ease-out"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                class="absolute inset-0 size-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
                            >
                        </template>

                        {{-- "view" hint on hover --}}
                        <span class="pointer-events-none absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-black/45 px-3 py-1.5 text-[12px] font-semibold text-white opacity-0 backdrop-blur-sm transition-opacity duration-200 group-hover:opacity-100">
                            {{ __('site.view') }}
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                        </span>
                    </a>

                    {{-- Prev / next arrows --}}
                    <template x-if="slides.length > 1">
                        <div>
                            <button type="button" @click="prev()" aria-label="Previous"
                                class="btn-tap absolute left-3 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full border border-brand-border bg-brand-surface/90 text-brand-text shadow-sm backdrop-blur hover:text-brand-accent">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6" /></svg>
                            </button>
                            <button type="button" @click="next()" aria-label="Next"
                                class="btn-tap absolute right-3 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-full border border-brand-border bg-brand-surface/90 text-brand-text shadow-sm backdrop-blur hover:text-brand-accent">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6" /></svg>
                            </button>
                        </div>
                    </template>

                    {{-- Delivery badge --}}
                    <div class="absolute -left-3.5 bottom-[26px] flex items-center gap-[11px] rounded-[14px] border border-brand-border bg-brand-surface px-4 py-[13px] shadow-[0_20px_40px_-24px_rgba(0,0,0,.4)]">
                        <span class="grid size-[38px] place-items-center rounded-[10px] bg-brand-accent-soft text-brand-accent">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                        </span>
                        <span class="max-w-[150px] text-[13px] font-semibold leading-snug">{{ __('site.hero_overlay') }}</span>
                    </div>

                    {{-- Dots --}}
                    <div class="mt-5 flex items-center justify-center gap-2" x-show="slides.length > 1">
                        <template x-for="(slide, i) in slides" :key="i">
                            <button type="button" @click="go(i)" :aria-label="'Slide ' + (i + 1)"
                                class="h-2 rounded-full transition-all duration-300"
                                :class="current === i ? 'w-6 bg-brand-accent' : 'w-2 bg-brand-border hover:bg-brand-muted'"></button>
                        </template>
                    </div>
                </div>
                @else
                    {{-- No featured products: static branded frame --}}
                    <div class="aspect-[4/5] rounded-[22px] border border-brand-border bg-brand-s2"></div>
                @endif
            </div>
        </section>

    {{-- Featured / bestsellers --}}
    <section class="mx-auto max-w-[1180px] px-6 pb-5 pt-10">
        <div class="mb-[26px] flex items-end justify-between gap-5">
            <div>
                <h2 class="font-newsreader text-[28px] font-normal leading-[1.05] -tracking-[.01em] sm:text-[38px]">{{ __('site.feat_title') }}</h2>
                <p class="mt-2 text-[15px] text-brand-muted">{{ __('site.feat_sub') }}</p>
            </div>
            <a href="{{ route('products') }}" wire:navigate class="whitespace-nowrap text-sm font-semibold text-brand-accent transition hover:opacity-75">{{ __('site.prod_title') }} →</a>
        </div>

        <div class="grid grid-cols-1 gap-[22px] sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $product)
                <x-storefront.product-card :product="$product" />
            @endforeach
        </div>
    </section>

    {{-- Values --}}
    <section class="mx-auto max-w-[1180px] px-6 py-14">
        <div class="border-t border-brand-border pt-12">
            <h2 class="mb-[30px] font-newsreader text-[26px] font-normal -tracking-[.01em] sm:text-[34px]">{{ __('site.values_title') }}</h2>
            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (__('site.values') as $value)
                    <div>
                        <span class="mb-4 grid size-[46px] place-items-center rounded-xl bg-brand-accent-soft text-brand-accent">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                        </span>
                        <h3 class="mb-[7px] text-lg font-semibold">{{ $value['t'] }}</h3>
                        <p class="text-[14.5px] leading-relaxed text-brand-muted text-pretty">{{ $value['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Story strip --}}
    <section class="mx-auto mb-[72px] max-w-[1180px] px-6">
        <div class="relative grid grid-cols-1 items-center gap-8 overflow-hidden rounded-[24px] bg-brand-accent px-6 py-10 text-brand-accent-ink sm:px-[52px] sm:py-14 lg:grid-cols-[1fr_auto]">
            <div class="relative z-[1]">
                <span class="text-xs font-semibold uppercase tracking-[.16em] opacity-80">{{ __('site.story_kicker') }}</span>
                <h2 class="mb-3.5 mt-3 font-newsreader text-[28px] font-normal leading-[1.1] -tracking-[.01em] sm:text-[40px] sm:leading-[1.08]">{{ __('site.story_title') }}</h2>
                <p class="max-w-[560px] text-[15px] leading-relaxed opacity-90 text-pretty sm:text-base">{{ __('site.story_body') }}</p>
            </div>
            <a href="{{ route('about') }}" wire:navigate class="btn-press relative z-[1] flex h-[52px] items-center justify-center whitespace-nowrap rounded-xl bg-brand-accent-ink px-7 text-[15px] font-semibold text-brand-accent">{{ __('site.story_cta') }}</a>
            <div class="absolute -right-[60px] -top-[60px] size-[260px] rounded-full bg-brand-accent-ink opacity-[.08]"></div>
        </div>
    </section>
</div>
