<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id'   => Category::factory(),
            'name'          => Str::title($name),
            'slug'          => Str::slug($name),
            'description'   => fake()->sentence(),
            'price'         => fake()->numberBetween(1000, 50000),
            'compare_price' => null,
            'stock'         => fake()->numberBetween(1, 50),
            'sku'           => 'SKU-' . fake()->unique()->numberBetween(1000, 99999),
            'images'        => [],
            'is_active'     => true,
            'featured'      => false,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
