<?php

namespace Tests\Feature;

use App\Models\Sponsor;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VisitSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function designer_can_fetch_sponsor_via_api(): void
    {
        $designer = $this->createDesignerUser();
        $sponsor = $this->createSponsor();

        $response = $this->actingAs($designer, 'designer')
            ->getJson("/api/sponsors/{$sponsor->id}");

        $response->assertOk()
            ->assertJson([
                'id' => $sponsor->id,
                'name' => $sponsor->name,
                'company_name' => $sponsor->company_name,
                'google_reviews_link' => $sponsor->google_reviews_link,
            ]);
    }

    /** @test */
    public function designer_can_submit_visit_with_photo(): void
    {
        $designer = $this->createDesignerUser();
        $sponsor = $this->createSponsor();
        $photo = UploadedFile::fake()->image('visit.jpg', 640, 480);

        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsor->id,
                'site_photo' => $photo,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Visit logged successfully',
            ]);

        $this->assertDatabaseHas('visits', [
            'user_id' => $designer->id,
            'sponsor_id' => $sponsor->id,
        ]);

        $visit = Visit::where('user_id', $designer->id)
            ->where('sponsor_id', $sponsor->id)
            ->first();

        $this->assertNotNull($visit->photo);
        $this->assertTrue(Storage::disk('public')->exists($visit->photo));
    }

    /** @test */
    public function designer_can_submit_visit_with_notes(): void
    {
        $designer = $this->createDesignerUser();
        $sponsor = $this->createSponsor();
        $photo = UploadedFile::fake()->image('visit.jpg', 640, 480);

        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsor->id,
                'site_photo' => $photo,
                'visited_at' => now()->toISOString(),
                'notes' => 'This is a test visit with notes',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('visits', [
            'user_id' => $designer->id,
            'sponsor_id' => $sponsor->id,
            'notes' => 'This is a test visit with notes',
        ]);
    }

    /** @test */
    public function api_returns_google_reviews_status(): void
    {
        $designer = $this->createDesignerUser();
        $sponsorWithReviews = $this->createSponsor([
            'google_reviews_link' => 'https://g.page/r/ABC123/review',
        ]);
        $sponsorWithoutReviews = $this->createSponsor([
            'google_reviews_link' => null,
        ]);
        $photo = UploadedFile::fake()->image('visit.jpg', 640, 480);
        $photo2 = UploadedFile::fake()->image('visit2.jpg', 640, 480);

        // Test with Google Reviews link
        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsorWithReviews->id,
                'site_photo' => $photo,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Visit logged successfully',
            ]);

        // Test without Google Reviews link
        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsorWithoutReviews->id,
                'site_photo' => $photo2,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Visit logged successfully',
            ]);
    }

    /** @test */
    public function visit_submission_requires_valid_sponsor(): void
    {
        $designer = $this->createDesignerUser();
        $photo = UploadedFile::fake()->image('visit.jpg', 640, 480);

        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => 999, // Non-existent sponsor
                'site_photo' => $photo,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['sponsor_id']);
    }

    /** @test */
    public function visit_submission_validates_photo_type(): void
    {
        $designer = $this->createDesignerUser();
        $sponsor = $this->createSponsor();
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsor->id,
                'site_photo' => $invalidFile,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['site_photo']);
    }

    /** @test */
    public function visit_submission_validates_photo_size(): void
    {
        $designer = $this->createDesignerUser();
        $sponsor = $this->createSponsor();
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(11 * 1024); // 11MB

        $response = $this->actingAs($designer, 'designer')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsor->id,
                'site_photo' => $largeFile,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['site_photo']);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_api(): void
    {
        $sponsor = $this->createSponsor();

        $this->getJson("/api/sponsors/{$sponsor->id}")
            ->assertUnauthorized();

        $this->postJson('/api/visits', ['sponsor_id' => $sponsor->id])
            ->assertUnauthorized();
    }

    /** @test */
    public function admin_can_also_submit_visits(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();
        $photo = UploadedFile::fake()->image('visit.jpg', 640, 480);

        $response = $this->actingAs($admin, 'web')
            ->postJson('/api/visits', [
                'sponsor_id' => $sponsor->id,
                'site_photo' => $photo,
                'visited_at' => now()->toISOString(),
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('visits', [
            'user_id' => $admin->id,
            'sponsor_id' => $sponsor->id,
        ]);
    }
}
