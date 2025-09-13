<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_admin_panel_when_authenticated(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->get('/admin');

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($admin, 'web');
    }

    /** @test */
    public function designer_can_access_designer_panel_when_authenticated(): void
    {
        $designer = $this->createDesignerUser();

        $response = $this->actingAs($designer, 'designer')
            ->get('/designer/dashboard');

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($designer, 'designer');
    }

    /** @test */
    public function admin_cannot_access_designer_panel(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->get('/designer/dashboard');

        $response->assertRedirect('/auth/designer/login');
    }

    /** @test */
    public function designer_cannot_access_admin_panel(): void
    {
        $designer = $this->createDesignerUser();

        $response = $this->actingAs($designer, 'designer')
            ->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');

        $response = $this->get('/designer/dashboard');
        $response->assertRedirect('/auth/designer/login');
    }

    /** @test */
    public function login_pages_are_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->get('/auth/designer/login');
        $response->assertStatus(200);
    }
}
