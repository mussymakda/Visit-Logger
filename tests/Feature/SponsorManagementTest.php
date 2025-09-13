<?php

namespace Tests\Feature;

use App\Filament\Resources\Sponsors\Pages\CreateSponsor;
use App\Filament\Resources\Sponsors\Pages\EditSponsor;
use App\Filament\Resources\Sponsors\Pages\ListSponsors;
use App\Models\Sponsor;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SponsorManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel('admin');
    }

    /** @test */
    public function admin_can_view_sponsors_list(): void
    {
        $admin = $this->createAdminUser();
        $sponsors = Sponsor::factory()->count(3)->create();

        $this->actingAs($admin, 'web');

        Livewire::test(ListSponsors::class)
            ->assertCanSeeTableRecords($sponsors);
    }

    /** @test */
    public function admin_can_create_sponsor(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web');

        $sponsorData = [
            'name' => 'John Doe',
            'company_name' => 'Acme Corp',
            'contact' => '+1234567890',
            'location' => '123 Main St, City, State',
            'description' => 'Test sponsor description',
            'google_reviews_link' => 'https://g.page/r/ABC123/review',
        ];

        Livewire::test(CreateSponsor::class)
            ->fillForm($sponsorData)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sponsors', $sponsorData);
    }

    /** @test */
    public function admin_can_edit_sponsor(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();

        $this->actingAs($admin, 'web');

        $newData = [
            'name' => 'Updated Name',
            'company_name' => 'Updated Company',
            'google_reviews_link' => 'https://g.page/r/UPDATED123/review',
        ];

        Livewire::test(EditSponsor::class, [
            'record' => $sponsor->getRouteKey(),
        ])
            ->fillForm($newData)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('sponsors', [
            'id' => $sponsor->id,
            'name' => 'Updated Name',
            'company_name' => 'Updated Company',
            'google_reviews_link' => 'https://g.page/r/UPDATED123/review',
        ]);
    }

    /** @test */
    public function admin_can_delete_sponsor(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();

        $this->actingAs($admin, 'web');

        Livewire::test(EditSponsor::class, [
            'record' => $sponsor->getRouteKey(),
        ])
            ->callAction(DeleteAction::class)
            ->assertHasNoFormErrors();

        $this->assertDatabaseMissing('sponsors', ['id' => $sponsor->id]);
    }

    /** @test */
    public function sponsor_validation_rules_work(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'web');

        Livewire::test(CreateSponsor::class)
            ->fillForm([
                'name' => '',
                'company_name' => '',
                'contact' => '',
                'location' => '',
                'google_reviews_link' => 'not-a-url',
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => 'required',
                'company_name' => 'required',
                'contact' => 'required',
                'location' => 'required',
                'google_reviews_link' => 'url',
            ]);
    }

    /** @test */
    public function sponsor_qr_code_is_generated_automatically(): void
    {
        $sponsor = $this->createSponsor();

        $this->assertNotNull($sponsor->qr_code);
        $this->assertStringContainsString('sponsor=', $sponsor->qr_code);
        $this->assertStringContainsString('/designer', $sponsor->qr_code);
    }

    /** @test */
    public function sponsor_search_functionality_works(): void
    {
        $admin = $this->createAdminUser();
        $sponsor1 = $this->createSponsor(['name' => 'John Doe', 'company_name' => 'Acme Corp']);
        $sponsor2 = $this->createSponsor(['name' => 'Jane Smith', 'company_name' => 'Tech Inc']);

        $this->actingAs($admin, 'web');

        Livewire::test(ListSponsors::class)
            ->searchTable('John')
            ->assertCanSeeTableRecords([$sponsor1])
            ->assertCanNotSeeTableRecords([$sponsor2])
            ->searchTable('Tech Inc')
            ->assertCanSeeTableRecords([$sponsor2])
            ->assertCanNotSeeTableRecords([$sponsor1]);
    }
}
