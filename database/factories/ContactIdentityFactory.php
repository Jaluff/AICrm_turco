<?php

namespace Database\Factories;

use App\Models\ContactIdentity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactIdentity>
 */
class ContactIdentityFactory extends Factory
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
            'contact_id' => \App\Models\Contact::factory(),
            'channel_type' => 'whatsapp_cloud',
            'external_id' => $this->faker->unique()->uuid(),
            'phone' => $this->faker->phoneNumber(),
            'username' => $this->faker->userName(),
            'metadata' => [],
        ];
    }
}
