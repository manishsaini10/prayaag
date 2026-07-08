<?php

namespace Database\Factories\Popup;

use App\Models\Popup\PopupCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PopupCategoryFactory extends Factory
{
    protected $model = PopupCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attr) => ['is_active' => false]);
    }
}
