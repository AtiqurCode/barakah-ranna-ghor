<?php

use App\Models\Product;
use App\Support\WhatsApp;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.storefront')] class extends Component
{
    public Product $product;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'related' => Product::ordered()
                ->whereKeyNot($this->product->getKey())
                ->take(3)
                ->get(),
            'orderLink' => WhatsApp::order($this->product->translate('name')),
            'chatLink' => WhatsApp::greeting(),
        ];
    }
};
?>

<div>
    <section class="mx-auto max-w-[1180px] px-6 pt-[34px]">
        <a href="{{ route('products') }}" wire:navigate class="inline-flex items-center gap-[7px] text-sm font-semibold text-brand-muted transition-colors hover:text-brand-accent">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>{{ __('site.back') }}
        </a>
    </section>

    <section class="mx-auto grid max-w-[1180px] grid-cols-1 items-start gap-8 px-6 pt-6 lg:grid-cols-2 lg:gap-[52px]">
        <div class="overflow-hidden rounded-[22px] border border-brand-border bg-brand-s2 lg:sticky lg:top-[90px]">
            <div class="aspect-square">
                <x-lazy-img :src="$product->image_url" :alt="$product->translate('name')" class="size-full" eager />
            </div>
        </div>

        <div>
            <span class="inline-block rounded-full bg-brand-accent-soft px-3 py-[5px] text-xs font-semibold text-brand-accent">{{ $product->categoryLabel() }}</span>
            <h1 class="mb-2 mt-4 font-newsreader text-[32px] font-normal leading-[1.08] -tracking-[.01em] sm:text-[46px] sm:leading-[1.05]">{{ $product->translate('name') }}</h1>
            <p class="text-[16px] text-brand-muted sm:text-[17px]">{{ $product->translate('tag') }}</p>

            <div class="my-6 flex items-baseline gap-1.5">
                <span class="text-[28px] font-semibold sm:text-[34px]">{{ $product->priceLabel() }}</span>
                <span class="text-base text-brand-muted">{{ $product->translate('unit') }}</span>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <form action="{{ route('checkout.store', $product) }}" method="POST" class="min-w-[200px] flex-1">
                    @csrf
                    <button type="submit" class="btn-press btn-glow flex h-[54px] w-full items-center justify-center rounded-[13px] bg-brand-accent text-[15.5px] font-semibold text-brand-accent-ink">
                        Buy now
                    </button>
                </form>
                <a href="{{ $orderLink }}" target="_blank" rel="noopener" class="btn-press flex h-[54px] flex-1 items-center justify-center gap-2.5 rounded-[13px] border border-brand-border bg-brand-surface px-[22px] text-[15px] font-semibold text-brand-text hover:border-brand-accent hover:text-brand-accent sm:min-w-[200px] sm:flex-none">
                    <x-icon.whatsapp class="size-[19px]" />{{ __('site.order') }}
                </a>
                <a href="{{ $chatLink }}" target="_blank" rel="noopener" class="btn-press flex h-[54px] flex-1 items-center justify-center rounded-[13px] border border-brand-border bg-brand-surface px-[22px] text-[15px] font-semibold text-brand-text hover:border-brand-accent hover:text-brand-accent sm:min-w-[200px] sm:flex-none">{{ __('site.pd_chat') }}</a>
            </div>

            <div class="mt-[34px] border-t border-brand-border pt-[26px]">
                <h3 class="mb-2.5 text-[15px] font-semibold tracking-[.02em]">{{ __('site.pd_about') }}</h3>
                <p class="text-[15.5px] leading-[1.7] text-brand-muted text-pretty">{{ $product->translate('description') }}</p>
            </div>

            <div class="mt-[26px]">
                <h3 class="mb-3 text-[15px] font-semibold tracking-[.02em]">{{ __('site.pd_details') }}</h3>
                <div class="flex flex-col gap-[11px]">
                    @foreach ($product->translate('details') as $detail)
                        <div class="flex items-start gap-[11px] text-[15px] leading-normal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand-accent)" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" class="mt-px flex-none"><path d="M20 6L9 17l-5-5" /></svg>{{ $detail }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Related --}}
    <section class="mx-auto max-w-[1180px] px-6 pb-[76px] pt-16">
        <h2 class="mb-6 font-newsreader text-[26px] font-normal -tracking-[.01em] sm:text-[32px]">{{ __('site.pd_related') }}</h2>
        <div class="grid grid-cols-1 gap-[22px] sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($related as $item)
                <x-storefront.product-card :product="$item" compact />
            @endforeach
        </div>
    </section>
</div>
