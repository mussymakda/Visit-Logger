<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_has_fillable_attributes(): void
    {
        $user = new User;

        $expected = [
            'name',
            'email',
            'password',
            'role',
        ];

        $this->assertEquals($expected, $user->getFillable());
    }

    /** @test */
    public function user_has_hidden_attributes(): void
    {
        $user = new User;

        $expected = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($expected, $user->getHidden());
    }

    /** @test */
    public function user_has_proper_casts(): void
    {
        $user = new User;
        $casts = $user->getCasts();

        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
    }

    /** @test */
    public function user_can_be_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEquals('admin', $admin->role);
    }

    /** @test */
    public function user_can_be_interior_designer(): void
    {
        $designer = User::factory()->designer()->create();

        $this->assertEquals('interior_designer', $designer->role);
    }

    /** @test */
    public function user_has_visits_relationship(): void
    {
        $user = User::factory()->create();
        $visit = Visit::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->visits()->exists());
        $this->assertEquals($visit->id, $user->visits->first()->id);
    }

    /** @test */
    public function user_factory_creates_valid_data(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->role);

        $this->assertIsString($user->name);
        $this->assertTrue(filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false);
        $this->assertContains($user->role, ['admin', 'interior_designer']);
    }

    /** @test */
    public function user_password_is_hashed(): void
    {
        $user = User::factory()->create();

        // Password should be hashed, not plain text
        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(password_verify('password', $user->password));
    }

    /** @test */
    public function user_email_is_unique(): void
    {
        $email = 'test@example.com';
        User::factory()->create(['email' => $email]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => $email]);
    }

    /** @test */
    public function user_can_have_multiple_visits(): void
    {
        $user = User::factory()->create();
        $visits = Visit::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertEquals(3, $user->visits()->count());
        $this->assertEquals($visits->pluck('id')->sort()->values(),
            $user->visits->pluck('id')->sort()->values());
    }

    /** @test */
    public function user_email_verified_at_defaults_to_now(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->email_verified_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    /** @test */
    public function user_can_be_unverified(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }
}
