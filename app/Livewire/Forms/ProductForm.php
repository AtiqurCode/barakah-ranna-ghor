<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Form;

class ProductForm extends Form
{
    public ?Product $product = null;

    public string $slug = '';

    public string $category = 'oils';

    public ?int $price = null;

    public bool $is_featured = false;

    public int $sort_order = 0;

    public string $image_url = '';

    public string $name_en = '';

    public string $name_bn = '';

    public string $tag_en = '';

    public string $tag_bn = '';

    public string $unit_en = '';

    public string $unit_bn = '';

    public string $description_en = '';

    public string $description_bn = '';

    public string $details_en = '';

    public string $details_bn = '';

    /**
     * Populate the form from an existing product (edit).
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
        $this->slug = $product->slug;
        $this->category = $product->category;
        $this->price = $product->price;
        $this->is_featured = $product->is_featured;
        $this->sort_order = $product->sort_order;
        $this->image_url = $product->image_url ?? '';

        foreach (['name', 'tag', 'unit', 'description'] as $field) {
            $this->{$field.'_en'} = $product->{$field}['en'] ?? '';
            $this->{$field.'_bn'} = $product->{$field}['bn'] ?? '';
        }

        $this->details_en = implode("\n", $product->details['en'] ?? []);
        $this->details_bn = implode("\n", $product->details['bn'] ?? []);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'alpha_dash', $this->uniqueSlug()],
            'category' => ['required', Rule::in(['oils', 'sweeteners', 'spices', 'dairy'])],
            'price' => ['required', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_bn' => ['required', 'string', 'max:255'],
            'tag_en' => ['nullable', 'string', 'max:255'],
            'tag_bn' => ['nullable', 'string', 'max:255'],
            'unit_en' => ['nullable', 'string', 'max:50'],
            'unit_bn' => ['nullable', 'string', 'max:50'],
            'description_en' => ['nullable', 'string'],
            'description_bn' => ['nullable', 'string'],
            'details_en' => ['nullable', 'string'],
            'details_bn' => ['nullable', 'string'],
        ];
    }

    private function uniqueSlug(): Unique
    {
        return Rule::unique('products', 'slug')->ignore($this->product);
    }

    /**
     * Persist the form to a new or existing product and return it.
     */
    public function save(): Product
    {
        $this->validate();

        $data = $this->payload();

        if ($this->product) {
            $this->product->update($data);

            return $this->product;
        }

        return Product::create($data);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'slug' => $this->slug,
            'category' => $this->category,
            'price' => $this->price,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'image_url' => $this->image_url ?: null,
            'name' => ['en' => $this->name_en, 'bn' => $this->name_bn],
            'tag' => ['en' => $this->tag_en, 'bn' => $this->tag_bn],
            'unit' => ['en' => $this->unit_en, 'bn' => $this->unit_bn],
            'description' => ['en' => $this->description_en, 'bn' => $this->description_bn],
            'details' => [
                'en' => $this->splitLines($this->details_en),
                'bn' => $this->splitLines($this->details_bn),
            ],
        ];
    }

    /**
     * Split a textarea value into a trimmed list of non-empty lines.
     *
     * @return array<int, string>
     */
    private function splitLines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
