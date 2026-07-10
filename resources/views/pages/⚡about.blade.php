<?php

use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.storefront')] class extends Component
{
    //
};
?>

<div>
    {{-- Intro --}}
    <section class="mx-auto grid max-w-[1180px] grid-cols-1 items-center gap-[52px] px-6 pt-16 lg:grid-cols-2">
        <div>
            <span class="text-xs font-semibold uppercase tracking-[.16em] text-brand-accent">{{ __('site.about_kicker') }}</span>
            <h1 class="mb-[18px] mt-3.5 font-newsreader text-[34px] font-normal leading-[1.08] -tracking-[.01em] text-balance sm:text-[50px] sm:leading-[1.04]">{{ __('site.about_title') }}</h1>
            <p class="mb-4 text-[15px] leading-[1.7] text-brand-muted text-pretty sm:text-base">{{ __('site.about_body1') }}</p>
            <p class="text-[15px] leading-[1.7] text-brand-muted text-pretty sm:text-base">{{ __('site.about_body2') }}</p>
        </div>
        <div class="aspect-[4/5] overflow-hidden rounded-[22px] border border-brand-border bg-brand-s2">
            <x-lazy-img :src="\App\Models\Product::where('slug', 'ghee')->value('image_url')" :alt="__('site.about_img')" class="size-full" eager />
        </div>
    </section>

    {{-- Stats --}}
    <section class="mx-auto max-w-[1180px] px-6 py-[60px]">
        <div class="grid grid-cols-2 gap-[22px] border-y border-brand-border py-10 md:grid-cols-4">
            @foreach (__('site.stats') as $stat)
                <div>
                    <div class="font-newsreader text-[34px] font-normal leading-none text-brand-accent sm:text-[46px]">{{ \App\Support\Digits::localize($stat['n']) }}</div>
                    <div class="mt-2 text-sm font-medium text-brand-muted">{{ $stat['l'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Values --}}
    <section class="mx-auto max-w-[1180px] px-6 pb-10">
        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (__('site.values') as $value)
                <div class="rounded-[18px] border border-brand-border bg-brand-surface p-7">
                    <span class="mb-4 grid size-[46px] place-items-center rounded-xl bg-brand-accent-soft text-brand-accent">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                    </span>
                    <h3 class="mb-[7px] text-lg font-semibold">{{ $value['t'] }}</h3>
                    <p class="text-[14.5px] leading-relaxed text-brand-muted text-pretty">{{ $value['d'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Mission --}}
    <section class="mx-auto mb-[76px] max-w-[1180px] px-6">
        <div class="rounded-[24px] bg-brand-s2 px-6 py-12 text-center sm:px-8 sm:py-14">
            <span class="text-xs font-semibold uppercase tracking-[.16em] text-brand-accent">{{ __('site.mission_title') }}</span>
            <p class="mx-auto mt-4 max-w-[640px] font-newsreader text-[22px] leading-[1.4] text-balance sm:text-[30px] sm:leading-[1.35]">{{ __('site.mission_body') }}</p>
        </div>
    </section>
</div>
