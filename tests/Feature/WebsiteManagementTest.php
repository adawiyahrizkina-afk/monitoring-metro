<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_route_opens_the_add_website_popup(): void
    {
        $response = $this->actingAs($this->admin())
            ->get(route('website.create'));

        $response->assertRedirect(route('website.index', ['action' => 'add']));
    }

    public function test_edit_route_opens_the_selected_website_popup(): void
    {
        $website = Website::create([
            'nama_website' => 'Website Tes',
            'instansi' => 'Instansi Tes',
            'url' => 'https://example.test',
        ]);

        $response = $this->actingAs($this->admin())
            ->get(route('website.edit', $website));

        $response->assertRedirect(route('website.index', ['edit' => $website->id]));
    }

    public function test_dashboard_reopens_the_add_popup_after_validation_errors(): void
    {
        $this->actingAs($this->admin())
            ->post(route('website.store'), [])
            ->assertSessionHasErrors(['nama_website', 'instansi', 'url']);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee("bukaPopup(document.getElementById('tambah'))", false);
    }

    public function test_monitoring_detail_link_targets_the_selected_website_history(): void
    {
        $website = Website::create([
            'nama_website' => 'Website Tes',
            'instansi' => 'Instansi Tes',
            'url' => 'https://example.test',
        ]);

        $this->actingAs($this->admin())
            ->get(route('monitoring.index'))
            ->assertOk()
            ->assertSee(route('riwayat.website', $website), false);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);
    }
}
