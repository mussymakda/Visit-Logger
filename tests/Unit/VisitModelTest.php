<?php

namespace Tests\Unit;

use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function visit_has_fillable_attributes(): void
    {
        $visit = new Visit;

        $expected = [
            'user_id',
            'sponsor_id',
            'photo',
            'notes',
            'visit_location',
            'visited_at',
        ];

        $this->assertEquals($expected, $visit->getFillable());
    }

    /** @test */
    public function visit_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $visit = Visit::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $visit->user);
        $this->assertEquals($user->id, $visit->user->id);
    }

    /** @test */
    public function visit_belongs_to_sponsor(): void
    {
        $sponsor = Sponsor::factory()->create();
        $visit = Visit::factory()->create(['sponsor_id' => $sponsor->id]);

        $this->assertInstanceOf(Sponsor::class, $visit->sponsor);
        $this->assertEquals($sponsor->id, $visit->sponsor->id);
    }

    /** @test */
    public function visit_can_have_photo(): void
    {
        $visit = Visit::factory()->withPhoto()->create();

        $this->assertNotNull($visit->photo);
        $this->assertStringContainsString('visit-photos/', $visit->photo);
    }

    /** @test */
    public function visit_can_have_empty_photo_string(): void
    {
        $visit = Visit::factory()->create(['photo' => '']);

        $this->assertEquals('', $visit->photo);
    }

    /** @test */
    public function visit_factory_creates_valid_data(): void
    {
        $visit = Visit::factory()->create();

        $this->assertNotNull($visit->user_id);
        $this->assertNotNull($visit->sponsor_id);
        $this->assertNotNull($visit->created_at);
        $this->assertNotNull($visit->updated_at);

        $this->assertInstanceOf(User::class, $visit->user);
        $this->assertInstanceOf(Sponsor::class, $visit->sponsor);
    }

    /** @test */
    public function visit_can_be_created_for_specific_user(): void
    {
        $user = User::factory()->create();
        $visit = Visit::factory()->forUser($user)->create();

        $this->assertEquals($user->id, $visit->user_id);
        $this->assertEquals($user->id, $visit->user->id);
    }

    /** @test */
    public function visit_can_be_created_for_specific_sponsor(): void
    {
        $sponsor = Sponsor::factory()->create();
        $visit = Visit::factory()->forSponsor($sponsor)->create();

        $this->assertEquals($sponsor->id, $visit->sponsor_id);
        $this->assertEquals($sponsor->id, $visit->sponsor->id);
    }

    /** @test */
    public function visit_timestamps_are_properly_set(): void
    {
        $visit = Visit::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $visit->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $visit->updated_at);

        // Updated_at should be same as created_at for new records
        $this->assertEquals($visit->created_at->toDateTimeString(),
            $visit->updated_at->toDateTimeString());
    }

    /** @test */
    public function visit_requires_user_and_sponsor(): void
    {
        // Test that foreign key constraints work
        $this->expectException(\Illuminate\Database\QueryException::class);

        Visit::create([
            'user_id' => 999, // Non-existent user
            'sponsor_id' => 999, // Non-existent sponsor
        ]);
    }

    /** @test */
    public function visit_hard_deletes(): void
    {
        $visit = Visit::factory()->create();
        $visitId = $visit->id;

        $visit->delete();

        // Check that it's actually deleted from database
        $this->assertDatabaseMissing('visits', ['id' => $visitId]);
        $this->assertNull(Visit::find($visitId));
    }

    /** @test */
    public function visit_photo_path_format(): void
    {
        $visit = Visit::factory()->withPhoto()->create();

        if ($visit->photo) {
            $this->assertMatchesRegularExpression('/^visit-photos\/.*\.(jpg|jpeg|png|gif)$/i', $visit->photo);
        }
    }
}
