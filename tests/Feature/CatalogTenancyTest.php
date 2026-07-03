<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactIdentity;
use App\Models\ContactReason;
use App\Models\Department;
use App\Models\Tag;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTenancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_catalogs_apply_company_id_trait(): void
    {
        $company = Company::factory()->create();
        Tenant::set($company);

        $department = Department::create(['name' => 'Support']);
        $tag = Tag::create(['name' => 'VIP']);
        $reason = ContactReason::create(['name' => 'Billing']);
        $contact = Contact::create(['name' => 'Alice']);
        $identity = ContactIdentity::create([
            'contact_id' => $contact->id,
            'channel_type' => 'whatsapp_cloud',
            'external_id' => '12345',
        ]);

        $this->assertEquals($company->id, $department->company_id);
        $this->assertEquals($company->id, $tag->company_id);
        $this->assertEquals($company->id, $reason->company_id);
        $this->assertEquals($company->id, $contact->company_id);
        $this->assertEquals($company->id, $identity->company_id);
    }

    public function test_catalogs_are_scoped_by_tenant(): void
    {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        // Create records for Company A
        Tenant::set($companyA);
        Department::create(['name' => 'Sales']);
        Tag::create(['name' => 'Hot']);
        ContactReason::create(['name' => 'Support Request']);
        $contactA = Contact::create(['name' => 'Bob']);

        // Create records for Company B
        Tenant::set($companyB);
        Department::create(['name' => 'Marketing']);
        Tag::create(['name' => 'Cold']);
        ContactReason::create(['name' => 'Job Application']);
        $contactB = Contact::create(['name' => 'Charlie']);

        // Set Tenant A and assert B's records are not visible
        Tenant::set($companyA);
        $this->assertCount(1, Department::all());
        $this->assertEquals('Sales', Department::first()->name);

        $this->assertCount(1, Tag::all());
        $this->assertEquals('Hot', Tag::first()->name);

        $this->assertCount(1, ContactReason::all());
        $this->assertEquals('Support Request', ContactReason::first()->name);

        $this->assertCount(1, Contact::all());
        $this->assertEquals('Bob', Contact::first()->name);
    }

    public function test_pivot_relationships_and_attributes(): void
    {
        $company = Company::factory()->create();
        Tenant::set($company);

        // 1. Department - User pivot
        $department = Department::create(['name' => 'Sales']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $department->users()->attach($user->id, ['receives_auto_assignment' => true]);

        $this->assertTrue($department->users->contains($user));
        $this->assertTrue($user->departments->contains($department));
        $this->assertTrue((bool) $department->users()->first()->pivot->receives_auto_assignment);

        // 2. Contact - Tag pivot
        $contact = Contact::create(['name' => 'Bob']);
        $tag = Tag::create(['name' => 'VIP']);
        $contact->tags()->attach($tag->id);

        $this->assertTrue($contact->tags->contains($tag));
        $this->assertTrue($tag->contacts->contains($contact));
    }

    public function test_jsonb_custom_fields_and_metadata(): void
    {
        $company = Company::factory()->create();
        Tenant::set($company);

        $customFields = ['crm_id' => '999', 'score' => 85];
        $contact = Contact::create([
            'name' => 'Alice',
            'custom_fields' => $customFields,
        ]);

        $this->assertEquals($customFields, $contact->fresh()->custom_fields);

        $metadata = ['session_id' => 'abc', 'browser' => 'Chrome'];
        $identity = ContactIdentity::create([
            'contact_id' => $contact->id,
            'channel_type' => 'whatsapp_cloud',
            'external_id' => 'ext-99',
            'metadata' => $metadata,
        ]);

        $this->assertEquals($metadata, $identity->fresh()->metadata);
    }
}
