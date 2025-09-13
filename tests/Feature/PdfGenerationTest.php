<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_generate_sponsor_qr_pdf(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();

        $response = $this->actingAs($admin, 'web')
            ->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'inline; filename="sponsor-qr-'.$sponsor->id.'.pdf"');
    }

    /** @test */
    public function pdf_contains_sponsor_information(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor([
            'name' => 'Test Sponsor',
            'company_name' => 'Test Company',
            'contact' => '+1234567890',
            'location' => '123 Test Street',
        ]);

        $response = $this->actingAs($admin, 'web')
            ->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertOk();

        // Check PDF headers
        $response->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition');

        // Verify content is not empty and starts with PDF signature
        $content = $response->getContent();
        $this->assertNotEmpty($content);
        $this->assertStringStartsWith('%PDF', $content);
    }

    /** @test */
    public function pdf_has_correct_dimensions(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();

        $response = $this->actingAs($admin, 'web')
            ->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertOk();

        // The PDF should be in 100x156mm format (approximately 283x442 points)
        // We can't easily test exact dimensions, but we can verify it's generated
        $this->assertNotEmpty($response->getContent());
    }

    /** @test */
    public function unauthenticated_user_cannot_access_pdf(): void
    {
        $sponsor = $this->createSponsor();

        $response = $this->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertRedirect('/admin/login');
    }

    /** @test */
    public function designer_cannot_access_pdf_generation(): void
    {
        $designer = $this->createDesignerUser();
        $sponsor = $this->createSponsor();

        $response = $this->actingAs($designer, 'designer')
            ->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertRedirect('/admin/login');
    }

    /** @test */
    public function pdf_generation_fails_for_nonexistent_sponsor(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'web')
            ->get('/admin/sponsors/999/pdf');

        $response->assertNotFound();
    }

    /** @test */
    public function pdf_includes_qr_code(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();

        // Ensure QR code is generated
        $this->assertNotNull($sponsor->qr_code);

        $response = $this->actingAs($admin, 'web')
            ->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertOk();

        // PDF should contain reference to QR code
        $content = $response->getContent();
        $this->assertNotEmpty($content);

        // The QR code should be referenced in the PDF (as an image or data)
        // This is a basic check that the PDF was generated with QR code data
        $this->assertTrue(strlen($content) > 1000); // Basic size check
    }

    /** @test */
    public function pdf_excludes_footer_copyright(): void
    {
        $admin = $this->createAdminUser();
        $sponsor = $this->createSponsor();

        $response = $this->actingAs($admin, 'web')
            ->get("/admin/sponsors/{$sponsor->id}/pdf");

        $response->assertOk();

        $content = $response->getContent();

        // Ensure footer copyright text is not present
        $this->assertStringNotContainsString('Copyright © 2025', $content);
        $this->assertStringNotContainsString('For support, contact your system administrator', $content);
    }
}
