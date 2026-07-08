<?php

use App\Livewire\Forms\ProductForm;
use App\Models\Product;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('Edit product')] class extends Component
{
    use WithFileUploads;

    public ProductForm $form;

    #[Validate('nullable|image|max:2048')]
    public $photo;

    public function mount(Product $product): void
    {
        $this->form->setProduct($product);
    }

    public function save(): void
    {
        if ($this->photo) {
            $this->form->image_url = Storage::url($this->photo->store('products', 'public'));
        }

        $this->form->save();

        Flux::toast(variant: 'success', text: __('Product updated.'));

        $this->redirectRoute('admin.products', navigate: true);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('Edit product') }}</flux:heading>
        <flux:text class="mt-1">{{ $form->name_en }}</flux:text>
    </div>

    @include('pages.admin.partials.product-form', ['submitLabel' => __('Save changes')])
</div>
