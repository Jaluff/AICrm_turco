<?php

namespace Database\Factories;

use App\Models\ContactReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactReason>
 */
class ContactReasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'name' => $this->faker->words(2, true),
            'color' => $this->faker->safeHexColor(),
            'active' => true,
        ];
    }
}
