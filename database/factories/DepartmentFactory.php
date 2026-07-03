<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
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
            'greeting_message' => $this->faker->sentence(),
            'farewell_message' => $this->faker->sentence(),
            'away_message' => $this->faker->sentence(),
            'auto_assignment_enabled' => false,
            'assign_offline_enabled' => false,
            'redistribute_unavailable_enabled' => false,
            'ai_enabled' => false,
        ];
    }
}
