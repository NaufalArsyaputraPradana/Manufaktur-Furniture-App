<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => Str::upper(Str::random(3)) . '-' . fake()->numberBetween(1000, 9999),
            'description' => fake()->paragraph(),
            'base_price' => fake()->numberBetween(50000, 5000000),
            'images' => [fake()->imageUrl(400, 300)],
            'dimensions' => fake()->numerify('## x ## x ## cm'),
            'wood_type' => fake()->word(),
            'finishing_type' => fake()->word(),
            'estimated_production_days' => fake()->numberBetween(1, 30),
            'is_active' => true,
            'is_customizable' => fake()->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create an active product.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive product.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a customizable product.
     */
    public function customizable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_customizable' => true,
        ]);
    }

    /**
     * Create a product without a base price.
     */
    public function withoutPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'base_price' => null,
        ]);
    }
}
