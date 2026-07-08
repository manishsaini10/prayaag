<?php

namespace Database\Factories\Popup;

use App\Models\Popup\Popup;
use App\Models\Popup\PopupLead;
use Illuminate\Database\Eloquent\Factories\Factory;

class PopupLeadFactory extends Factory
{
    protected $model = PopupLead::class;

    public function definition(): array
    {
        return [
            'popup_id' => Popup::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'form_data' => ['name' => $this->faker->name(), 'email' => $this->faker->safeEmail(), 'message' => $this->faker->sentence()],
            'status' => $this->faker->randomElement(['new', 'contacted', 'qualified', 'converted', 'lost']),
            'source' => $this->faker->url(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
