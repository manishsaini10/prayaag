<?php

namespace Database\Factories\Popup;

use App\Models\Popup\Popup;
use App\Models\Popup\PopupCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PopupFactory extends Factory
{
    protected $model = Popup::class;

    public function definition(): array
    {
        $types = ['modal', 'slide_in', 'floating_bar', 'announcement_bar', 'exit_intent', 'newsletter', 'welcome'];
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement(['draft', 'active', 'paused']),
            'category_id' => PopupCategory::factory(),
            'structure' => [
                'container' => ['type' => 'container', 'styles' => ['padding' => '30', 'background' => '#ffffff']],
                'rows' => [
                    ['columns' => [['width' => 12, 'widgets' => [
                        ['type' => 'heading', 'content' => $this->faker->sentence, 'settings' => ['tag' => 'h2', 'align' => 'center']],
                        ['type' => 'paragraph', 'content' => $this->faker->paragraph, 'settings' => ['align' => 'center']],
                        ['type' => 'button', 'content' => 'Learn More', 'settings' => ['align' => 'center', 'backgroundColor' => '#6366f1']],
                    ]]]]
                ]
            ],
            'settings' => ['width' => 600, 'overlay' => true, 'close_button' => true, 'animation' => 'fade'],
            'design' => [],
            'styles' => [],
            'frequency_type' => 'once_per_session',
            'frequency_delay' => 0,
            'priority' => 0,
            'view_count' => $this->faker->numberBetween(0, 10000),
            'impression_count' => $this->faker->numberBetween(0, 15000),
            'click_count' => $this->faker->numberBetween(0, 5000),
            'conversion_count' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attr) => ['status' => 'active']);
    }

    public function draft(): static
    {
        return $this->state(fn(array $attr) => ['status' => 'draft']);
    }

    public function scheduled(): static
    {
        return $this->state(fn(array $attr) => [
            'status' => 'scheduled',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addMonth(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn(array $attr) => [
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
        ]);
    }

    public function ofType(string $type): static
    {
        return $this->state(fn(array $attr) => ['type' => $type]);
    }
}
