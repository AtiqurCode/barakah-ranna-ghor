<?php

use App\Models\Product;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Products')] class extends Component
{
    use WithPagination;

    public function toggleFeatured(Product $product): void
    {
        $product->update(['is_featured' => ! $product->is_featured]);
    }

    public function delete(Product $product): void
    {
        $product->delete();

        Flux::toast(variant: 'success', text: __('Product deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        return [
            'products' => Product::ordered()->paginate(15),
        ];
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Products') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Manage your storefront catalogue.') }}</flux:text>
        </div>
        <flux:button :href="route('admin.products.create')" wire:navigate variant="primary" icon="plus">{{ __('New product') }}</flux:button>
    </div>

    <flux:table :paginate="$products">
        <flux:table.columns>
            <flux:table.column>{{ __('Product') }}</flux:table.column>
            <flux:table.column>{{ __('Category') }}</flux:table.column>
            <flux:table.column>{{ __('Price') }}</flux:table.column>
            <flux:table.column>{{ __('Featured') }}</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            <x-lazy-img :src="$product->image_url" alt="" class="size-10 shrink-0 rounded-lg" />
                            <div>
                                <div class="font-medium">{{ $product->name['en'] ?? '' }}</div>
                                <div class="text-xs text-zinc-500">{{ $product->name['bn'] ?? '' }}</div>
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="zinc">{{ __('site.categories.'.$product->category, [], 'en') }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell variant="strong">৳{{ $product->price }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:button
                            size="xs"
                            variant="{{ $product->is_featured ? 'primary' : 'ghost' }}"
                            icon="star"
                            wire:click="toggleFeatured({{ $product->id }})"
                        >{{ $product->is_featured ? __('Yes') : __('No') }}</flux:button>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex justify-end gap-1">
                            <flux:button size="xs" :href="route('product', $product)" target="_blank" icon="eye" variant="ghost" />
                            <flux:button size="xs" :href="route('admin.products.edit', $product)" wire:navigate icon="pencil-square" variant="ghost" />
                            <flux:button
                                size="xs"
                                icon="trash"
                                variant="ghost"
                                wire:click="delete({{ $product->id }})"
                                wire:confirm="{{ __('Delete this product? This cannot be undone.') }}"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
