<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'category' => $this->faker->randomElement(['oils', 'sweeteners', 'spices', 'dairy']),
            'price' => $this->faker->numberBetween(100, 1000),
            'image_url' => 'https://placehold.co/900x900',
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_featured' => false,
            'name' => ['en' => Str::title($name), 'bn' => 'পণ্য'],
            'tag' => ['en' => $this->faker->sentence(3), 'bn' => 'ট্যাগ'],
            'unit' => ['en' => ' / unit', 'bn' => ' / একক'],
            'description' => ['en' => $this->faker->paragraph(), 'bn' => 'বিবরণ'],
            'details' => ['en' => $this->faker->words(4), 'bn' => ['বিবরণ']],
        ];
    }

    /**
     * Mark the product as a featured bestseller.
     */
    public function featured(): static
    {
        return $this->state(fn (): array => ['is_featured' => true]);
    }
}
