@props([
    'product',
    'compact' => false,
])

<a
    href="{{ route('product', $product) }}"
    wire:navigate
    {{ $attributes->class('group flex flex-col overflow-hidden rounded-[18px] border border-brand-border bg-brand-surface transition duration-[250ms] hover:-translate-y-[5px] hover:border-brand-accent hover:shadow-[0_24px_48px_-30px_rgba(0,0,0,.4)]') }}
>
    <div class="relative aspect-square bg-brand-s2">
        <x-lazy-img :src="$product->image_url" :alt="$product->translate('name')" class="size-full" />
        @unless ($compact)
            <span class="absolute left-[13px] top-[13px] rounded-full px-[11px] py-[5px] text-[11px] font-semibold text-brand-accent backdrop-blur-sm" style="background: color-mix(in srgb, var(--brand-surface) 82%, transparent)">{{ $product->categoryLabel() }}</span>
        @endunless
    </div>

    <div class="flex flex-1 flex-col gap-1.5 {{ $compact ? 'p-4' : 'px-[17px] pb-[19px] pt-[17px]' }}">
        <h3 class="{{ $compact ? 'text-base' : 'text-[17px]' }} font-semibold">{{ $product->translate('name') }}</h3>

        @unless ($compact)
            <p class="text-[13.5px] leading-normal text-brand-muted">{{ $product->translate('tag') }}</p>
        @endunless

        <div class="mt-auto flex items-center justify-between {{ $compact ? 'pt-2.5' : 'pt-3.5' }}">
            <span class="{{ $compact ? 'text-base' : 'text-[17px]' }} font-semibold">
                {{ $product->priceLabel() }}<span class="text-[12.5px] font-normal text-brand-muted">{{ $product->translate('unit') }}</span>
            </span>
            <span class="text-[13px] font-semibold text-brand-accent">{{ __('site.view') }} →</span>
        </div>
    </div>
</a>
