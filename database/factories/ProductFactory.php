<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $cost = fake()->numberBetween(500, 5000);

        return [
            'code' => strtoupper(fake()->unique()->bothify('SH-####')),
            'name' => fake()->words(3, true),
            'description' => null,
            'color' => fake()->safeColorName(),
            'size' => (string) fake()->numberBetween(35, 45),
            'cost' => $cost,
            'price' => $cost + 1000,
            // Plenty of stock by default so tests that are not about stock can invoice freely.
            'stock' => 1000,
            'is_active' => true,
        ];
    }

    public function stock(int $units): static
    {
        return $this->state(fn () => ['stock' => $units]);
    }
}
