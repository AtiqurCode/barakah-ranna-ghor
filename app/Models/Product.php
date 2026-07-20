<?php

namespace App\Models;

use App\Support\Digits;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'category',
        'price',
        'image_url',
        'sort_order',
        'is_featured',
        'name',
        'tag',
        'unit',
        'description',
        'details',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_featured' => 'boolean',
            'name' => 'array',
            'tag' => 'array',
            'unit' => 'array',
            'description' => 'array',
            'details' => 'array',
        ];
    }

    /**
     * Resolve a translatable attribute for the active locale, falling back to English.
     *
     * @return string|array<int, string>
     */
    public function translate(string $attribute): string|array
    {
        $value = $this->{$attribute};

        if (! is_array($value)) {
            return $value ?? '';
        }

        return $value[app()->getLocale()] ?? $value['en'] ?? reset($value) ?: '';
    }

    /**
     * The localized, currency-prefixed price (with Bengali digits when applicable).
     */
    public function priceLabel(): string
    {
        return '$'.Digits::localize($this->price);
    }

    /**
     * The translated category label from the site translation strings.
     */
    public function categoryLabel(): string
    {
        return __('site.categories.'.$this->category);
    }

    /**
     * Use the slug for route-model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
