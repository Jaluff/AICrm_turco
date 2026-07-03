<?php

namespace Database\Factories;

use App\Models\ChannelConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChannelConnection>
 */
use App\Models\Company;

class ChannelConnectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'type' => 'whatsapp_cloud',
            'name' => $this->faker->word() . ' WhatsApp Connection',
            'status' => 'active',
            'external_business_id' => $this->faker->numerify('##############'),
            'external_phone_number_id' => $this->faker->numerify('##############'),
            'external_waba_id' => $this->faker->numerify('##############'),
            'phone_number' => $this->faker->e164PhoneNumber(),
            'access_token' => 'fake_access_token_1234567890',
            'verify_token' => 'fake_verify_token_1234567890',
            'app_secret' => 'fake_app_secret_1234567890',
            'greeting_message' => '¡Hola! Bienvenido a nuestro canal.',
            'farewell_message' => '¡Gracias por comunicarte con nosotros! Adiós.',
            'metadata' => ['env' => 'testing'],
        ];
    }
}
