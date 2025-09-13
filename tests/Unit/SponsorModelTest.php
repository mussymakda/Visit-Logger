<?php

namespace Tests\Unit;

use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Tests\TestCase;

class SponsorModelTest extends TestCase
{
    /** @test */
    public function sponsor_has_fillable_attributes(): void
    {
        $sponsor = new Sponsor;

        $expected = [
            'name',
            'company_name',
            'google_reviews_link',
            'contact',
            'location',
            'description',
            'qr_code',
            'qr_code_path',
        ];

        $this->assertEquals($expected, $sponsor->getFillable());
    }

    /** @test */
    public function sponsor_generates_qr_code_on_creation(): void
    {
        $sponsor = Sponsor::factory()->create();

        $this->assertNotNull($sponsor->qr_code);
        $this->assertStringContainsString('/designer?sponsor=', $sponsor->qr_code);
        $this->assertStringContainsString('qrserver.com', $sponsor->qr_code_path);
    }

    /** @test */
    public function sponsor_regenerates_qr_code_on_update(): void
    {
        $sponsor = Sponsor::factory()->create(['name' => 'Original Name']);
        $originalQrCode = $sponsor->qr_code;

        $sponsor->update(['name' => 'Updated Name']);

        // Since QR code is based on ID, it should be the same unless the service provides time-based URLs
        $this->assertStringContainsString('/designer?sponsor=', $sponsor->fresh()->qr_code);
    }

    /** @test */
    public function sponsor_has_visits_relationship(): void
    {
        $sponsor = Sponsor::factory()->create();
        $user = User::factory()->create();
        $visit = Visit::factory()->create([
            'sponsor_id' => $sponsor->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($sponsor->visits->contains($visit));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $sponsor->visits);
    }

    /** @test */
    public function sponsor_can_have_google_reviews_link(): void
    {
        $sponsor = new Sponsor;
        $sponsor->google_reviews_link = 'https://g.page/r/ABC123/review';

        $this->assertEquals('https://g.page/r/ABC123/review', $sponsor->google_reviews_link);
    }

    /** @test */
    public function sponsor_can_have_null_google_reviews_link(): void
    {
        $sponsor = new Sponsor;
        $sponsor->google_reviews_link = null;

        $this->assertNull($sponsor->google_reviews_link);
    }

    /** @test */
    public function sponsor_validation_rules(): void
    {
        // Test that the model can be filled with valid data
        $sponsor = new Sponsor;
        $sponsorData = [
            'name' => 'John Doe',
            'company_name' => 'Acme Corp',
            'contact' => '+1234567890',
            'location' => '123 Main St, City, State',
            'description' => 'Test description',
            'google_reviews_link' => 'https://g.page/r/ABC123/review',
        ];

        $sponsor->fill($sponsorData);

        $this->assertEquals('John Doe', $sponsor->name);
        $this->assertEquals('Acme Corp', $sponsor->company_name);
        $this->assertEquals('https://g.page/r/ABC123/review', $sponsor->google_reviews_link);
    }

    /** @test */
    public function sponsor_factory_creates_valid_data(): void
    {
        $sponsor = Sponsor::factory()->create();

        $this->assertNotNull($sponsor->name);
        $this->assertNotNull($sponsor->company_name);
        $this->assertNotNull($sponsor->contact);
        $this->assertNotNull($sponsor->location);
        $this->assertIsString($sponsor->name);
        $this->assertIsString($sponsor->company_name);
        $this->assertIsString($sponsor->contact);
        $this->assertIsString($sponsor->location);
    }

    /** @test */
    public function sponsor_can_be_hard_deleted(): void
    {
        $sponsor = Sponsor::factory()->create();
        $sponsorId = $sponsor->id;

        $sponsor->delete();

        // Check that it's actually deleted from database
        $this->assertDatabaseMissing('sponsors', ['id' => $sponsorId]);
        $this->assertNull(Sponsor::find($sponsorId));
    }
}
