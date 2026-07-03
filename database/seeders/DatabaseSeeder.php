<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear Empresa Alfa
        $alfa = \App\Models\Company::create([
            'name' => 'Empresa Alfa',
            'slug' => 'empresa-alfa',
            'timezone' => 'America/Argentina/Buenos_Aires',
            'default_language' => 'es',
        ]);

        // Crear usuarios para Empresa Alfa
        User::factory()->admin()->create([
            'company_id' => $alfa->id,
            'name' => 'Admin Alfa',
            'email' => 'admin@alfa.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        User::factory()->agent()->create([
            'company_id' => $alfa->id,
            'name' => 'Agente Alfa',
            'email' => 'agent@alfa.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        // Crear Empresa Beta
        $beta = \App\Models\Company::create([
            'name' => 'Empresa Beta',
            'slug' => 'empresa-beta',
            'timezone' => 'America/Argentina/Buenos_Aires',
            'default_language' => 'es',
        ]);

        // Crear usuarios para Empresa Beta
        User::factory()->admin()->create([
            'company_id' => $beta->id,
            'name' => 'Admin Beta',
            'email' => 'admin@beta.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        User::factory()->agent()->create([
            'company_id' => $beta->id,
            'name' => 'Agente Beta',
            'email' => 'agent@beta.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }
}
