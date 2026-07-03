<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear tenant context before each test
        Tenant::clear();
    }

    public function test_relationships(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->assertTrue($company->users->contains($user));
        $this->assertEquals($company->id, $user->company->id);
    }

    public function test_global_scope_filters_by_tenant(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $userA = User::factory()->create(['company_id' => $companyA->id]);
        $userB = User::factory()->create(['company_id' => $companyB->id]);

        // Set Tenant A
        Tenant::set($companyA);

        $users = User::all();
        $this->assertCount(1, $users);
        $this->assertTrue($users->contains($userA));
        $this->assertFalse($users->contains($userB));

        // Set Tenant B
        Tenant::set($companyB);

        $users = User::all();
        $this->assertCount(1, $users);
        $this->assertTrue($users->contains($userB));
        $this->assertFalse($users->contains($userA));
    }

    public function test_sets_tenant_id_automatically_on_creating(): void
    {
        $company = Company::factory()->create();
        Tenant::set($company);

        // Create user without company_id
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->assertEquals($company->id, $user->company_id);
    }

    public function test_middleware_sets_tenant_from_authenticated_user(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $this->actingAs($user);

        // La ruta '/' pasa por el grupo de middleware 'web'
        $response = $this->get('/');
        $response->assertStatus(200);

        $this->assertEquals($company->id, Tenant::id());
    }
}
