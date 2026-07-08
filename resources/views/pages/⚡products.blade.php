<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.storefront')] class extends Component
{
    #[Url]
    public string $filter = 'all';

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $products = Product::ordered()
            ->when($this->filter !== 'all', fn ($query) => $query->where('category', $this->filter))
            ->get();

        return [
            'products' => $products,
            'categories' => ['all', 'oils', 'sweeteners', 'spices', 'dairy'],
        ];
    }
};
?>

<div>
    <section class="mx-auto max-w-[1180px] px-6 pt-[60px]">
        <h1 class="font-newsreader text-[48px] font-normal -tracking-[.01em]">{{ __('site.prod_title') }}</h1>
        <p class="mb-[30px] mt-2.5 text-base text-brand-muted">{{ __('site.prod_sub') }}</p>

        <div class="mb-[30px] flex flex-wrap gap-2.5">
            @foreach ($categories as $category)
                <button
                    type="button"
                    wire:click="$set('filter', '{{ $category }}')"
                    class="rounded-full border px-[18px] py-[9px] text-[13.5px] font-semibold transition
                        {{ $filter === $category
                            ? 'border-brand-accent bg-brand-accent text-brand-accent-ink'
                            : 'border-brand-border bg-brand-surface text-brand-text' }}"
                >{{ __('site.categories.'.$category) }}</button>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-[1180px] px-6 pb-[76px]">
        <div class="grid grid-cols-1 gap-[22px] sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <x-storefront.product-card :product="$product" wire:key="product-{{ $product->id }}" />
            @endforeach
        </div>
    </section>
</div>
