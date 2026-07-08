{{-- Shared create/edit form. Expects: $submitLabel. Uses $this->form (ProductForm) and $this->photo. --}}
<form wire:submit="save" class="flex flex-col gap-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main details --}}
        <div class="flex flex-col gap-5 lg:col-span-2">
            <flux:card class="flex flex-col gap-5">
                <flux:heading size="lg">{{ __('Details') }}</flux:heading>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label badge="EN">{{ __('Name') }}</flux:label>
                        <flux:input wire:model="form.name_en" />
                        <flux:error name="form.name_en" />
                    </flux:field>
                    <flux:field>
                        <flux:label badge="বাংলা">{{ __('Name') }}</flux:label>
                        <flux:input wire:model="form.name_bn" dir="auto" />
                        <flux:error name="form.name_bn" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="EN">{{ __('Tagline') }}</flux:label>
                        <flux:input wire:model="form.tag_en" />
                        <flux:error name="form.tag_en" />
                    </flux:field>
                    <flux:field>
                        <flux:label badge="বাংলা">{{ __('Tagline') }}</flux:label>
                        <flux:input wire:model="form.tag_bn" dir="auto" />
                        <flux:error name="form.tag_bn" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="EN">{{ __('Unit') }}</flux:label>
                        <flux:input wire:model="form.unit_en" placeholder=" / 500g" />
                        <flux:error name="form.unit_en" />
                    </flux:field>
                    <flux:field>
                        <flux:label badge="বাংলা">{{ __('Unit') }}</flux:label>
                        <flux:input wire:model="form.unit_bn" dir="auto" placeholder=" / ৫০০গ্রা" />
                        <flux:error name="form.unit_bn" />
                    </flux:field>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:field>
                        <flux:label badge="EN">{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="form.description_en" rows="4" />
                        <flux:error name="form.description_en" />
                    </flux:field>
                    <flux:field>
                        <flux:label badge="বাংলা">{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="form.description_bn" rows="4" dir="auto" />
                        <flux:error name="form.description_bn" />
                    </flux:field>

                    <flux:field>
                        <flux:label badge="EN">{{ __('Details') }}</flux:label>
                        <flux:textarea wire:model="form.details_en" rows="4" placeholder="{{ __('One item per line') }}" />
                        <flux:description>{{ __('One item per line.') }}</flux:description>
                        <flux:error name="form.details_en" />
                    </flux:field>
                    <flux:field>
                        <flux:label badge="বাংলা">{{ __('Details') }}</flux:label>
                        <flux:textarea wire:model="form.details_bn" rows="4" dir="auto" placeholder="{{ __('One item per line') }}" />
                        <flux:description>{{ __('One item per line.') }}</flux:description>
                        <flux:error name="form.details_bn" />
                    </flux:field>
                </div>
            </flux:card>
        </div>

        {{-- Sidebar: image + meta --}}
        <div class="flex flex-col gap-5">
            <flux:card class="flex flex-col gap-4">
                <flux:heading size="lg">{{ __('Image') }}</flux:heading>

                <div class="aspect-square overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="" class="size-full object-cover">
                    @elseif ($form->image_url)
                        <img src="{{ $form->image_url }}" alt="" class="size-full object-cover">
                    @else
                        <div class="flex size-full items-center justify-center text-sm text-zinc-400">{{ __('No image') }}</div>
                    @endif
                </div>

                <flux:field>
                    <flux:label>{{ __('Upload image') }}</flux:label>
                    <flux:input type="file" wire:model="photo" accept="image/*" />
                    <flux:error name="photo" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Or image URL') }}</flux:label>
                    <flux:input wire:model="form.image_url" placeholder="https://..." />
                    <flux:error name="form.image_url" />
                </flux:field>
            </flux:card>

            <flux:card class="flex flex-col gap-4">
                <flux:heading size="lg">{{ __('Attributes') }}</flux:heading>

                <flux:field>
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <flux:input wire:model="form.slug" placeholder="{{ __('auto-generated from name') }}" />
                    <flux:error name="form.slug" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Category') }}</flux:label>
                    <flux:select wire:model="form.category">
                        <flux:select.option value="oils">{{ __('Oils') }}</flux:select.option>
                        <flux:select.option value="sweeteners">{{ __('Sweeteners') }}</flux:select.option>
                        <flux:select.option value="spices">{{ __('Spices') }}</flux:select.option>
                        <flux:select.option value="dairy">{{ __('Dairy') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="form.category" />
                </flux:field>

                <div class="grid grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label>{{ __('Price (৳)') }}</flux:label>
                        <flux:input type="number" wire:model="form.price" min="0" />
                        <flux:error name="form.price" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Sort order') }}</flux:label>
                        <flux:input type="number" wire:model="form.sort_order" min="0" />
                        <flux:error name="form.sort_order" />
                    </flux:field>
                </div>

                <flux:field variant="inline">
                    <flux:label>{{ __('Featured (bestseller)') }}</flux:label>
                    <flux:switch wire:model="form.is_featured" />
                </flux:field>
            </flux:card>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">{{ $submitLabel }}</flux:button>
        <flux:button :href="route('admin.products')" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
    </div>
</form>
