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
        return [
            'featured' => Product::featured()->ordered()->get(),
            'hero' => Product::featured()->ordered()->first(),
        ];
    }
};
?>

<div>
    {{-- Hero --}}
    <section>
        <div class="mx-auto grid max-w-[1180px] grid-cols-1 items-center gap-14 px-6 pb-11 pt-[76px] lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-accent-soft px-3.5 py-[7px] text-[12.5px] font-semibold text-brand-accent">
                    <span class="size-1.5 rounded-full bg-brand-accent"></span>{{ __('site.hero_badge') }}
                </span>
                <h1 class="mt-[22px] font-newsreader text-[46px] font-normal leading-[1.04] -tracking-[.01em] text-balance sm:text-[60px]">{{ __('site.hero_title') }}</h1>
                <p class="mt-[22px] max-w-[460px] text-[17px] leading-relaxed text-brand-muted text-pretty">{{ __('site.hero_sub') }}</p>

                <div class="mt-[30px] flex flex-wrap gap-3">
                    <a href="{{ route('products') }}" wire:navigate class="flex h-[50px] items-center rounded-xl bg-brand-accent px-[26px] text-[15px] font-semibold text-brand-accent-ink transition hover:brightness-105">{{ __('site.hero_cta1') }}</a>
                    <a href="{{ route('about') }}" wire:navigate class="flex h-[50px] items-center rounded-xl border border-brand-border bg-brand-surface px-[26px] text-[15px] font-semibold text-brand-text transition hover:border-brand-accent hover:text-brand-accent">{{ __('site.hero_cta2') }} →</a>
                </div>

                <div class="mt-[34px] flex flex-wrap gap-2.5">
                    @foreach (__('site.trust') as $chip)
                        <span class="inline-flex items-center gap-[7px] text-[13px] font-medium text-brand-muted">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--brand-accent)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>{{ $chip }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <div class="relative aspect-[4/5] overflow-hidden rounded-[22px] border border-brand-border bg-brand-s2 shadow-[0_40px_80px_-50px_rgba(0,0,0,.4)]">
                    <img src="{{ $hero?->image_url }}" alt="{{ $hero?->translate('name') }}" class="size-full object-cover">
                </div>
                <div class="absolute -left-3.5 bottom-[26px] flex items-center gap-[11px] rounded-[14px] border border-brand-border bg-brand-surface px-4 py-[13px] shadow-[0_20px_40px_-24px_rgba(0,0,0,.4)]">
                    <span class="grid size-[38px] place-items-center rounded-[10px] bg-brand-accent-soft text-brand-accent">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                    </span>
                    <span class="max-w-[150px] text-[13px] font-semibold leading-snug">{{ __('site.hero_overlay') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured / bestsellers --}}
    <section class="mx-auto max-w-[1180px] px-6 pb-5 pt-10">
        <div class="mb-[26px] flex items-end justify-between gap-5">
            <div>
                <h2 class="font-newsreader text-[38px] font-normal leading-[1.05] -tracking-[.01em]">{{ __('site.feat_title') }}</h2>
                <p class="mt-2 text-[15px] text-brand-muted">{{ __('site.feat_sub') }}</p>
            </div>
            <a href="{{ route('products') }}" wire:navigate class="whitespace-nowrap text-sm font-semibold text-brand-accent hover:opacity-75">{{ __('site.prod_title') }} →</a>
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
            <h2 class="mb-[30px] font-newsreader text-[34px] font-normal -tracking-[.01em]">{{ __('site.values_title') }}</h2>
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
        <div class="relative grid grid-cols-1 items-center gap-8 overflow-hidden rounded-[24px] bg-brand-accent px-8 py-14 text-brand-accent-ink sm:px-[52px] lg:grid-cols-[1fr_auto]">
            <div class="relative z-[1]">
                <span class="text-xs font-semibold uppercase tracking-[.16em] opacity-80">{{ __('site.story_kicker') }}</span>
                <h2 class="mb-3.5 mt-3 font-newsreader text-[40px] font-normal leading-[1.08] -tracking-[.01em]">{{ __('site.story_title') }}</h2>
                <p class="max-w-[560px] text-base leading-relaxed opacity-90 text-pretty">{{ __('site.story_body') }}</p>
            </div>
            <a href="{{ route('about') }}" wire:navigate class="relative z-[1] flex h-[52px] items-center whitespace-nowrap rounded-xl bg-brand-accent-ink px-7 text-[15px] font-semibold text-brand-accent transition-transform hover:-translate-y-0.5">{{ __('site.story_cta') }}</a>
            <div class="absolute -right-[60px] -top-[60px] size-[260px] rounded-full bg-brand-accent-ink opacity-[.08]"></div>
        </div>
    </section>
</div>
