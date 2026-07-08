<?php

use App\Livewire\Forms\ProductForm;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] #[Title('New product')] class extends Component
{
    use WithFileUploads;

    public ProductForm $form;

    #[Validate('nullable|image|max:2048')]
    public $photo;

    public function save(): void
    {
        if (trim($this->form->slug) === '') {
            $this->form->slug = Str::slug($this->form->name_en);
        }

        if ($this->photo) {
            $this->form->image_url = Storage::url($this->photo->store('products', 'public'));
        }

        $this->form->save();

        Flux::toast(variant: 'success', text: __('Product created.'));

        $this->redirectRoute('admin.products', navigate: true);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    <div>
        <flux:heading size="xl">{{ __('New product') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Add a product to the catalogue.') }}</flux:text>
    </div>

    @include('pages.admin.partials.product-form', ['submitLabel' => __('Create product')])
</div>
