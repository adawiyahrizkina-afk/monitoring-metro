<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MonitoringCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_website_check_returns_json_and_records_the_result(): void
    {
        Http::fake([
            'https://example.test/*' => Http::response('OK', 200),
        ]);

        $website = $this->createWebsite();

        $response = $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
        ]))->postJson(route('website.check', $website));

        $response->assertOk()
            ->assertJsonPath('id', $website->id)
            ->assertJsonPath('status', 'Online');

        $this->assertDatabaseHas('monitoring_logs', [
            'website_id' => $website->id,
            'nama_website' => $website->nama_website,
            'url' => $website->url,
            'status' => 'Online',
        ]);
    }

    public function test_realtime_check_returns_results_for_active_websites(): void
    {
        Http::fake([
            'https://example.test/*' => Http::response('OK', 200),
        ]);

        $website = $this->createWebsite();

        $response = $this->actingAs(User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
        ]))->postJson(route('monitoring.realtime'));

        $response->assertOk()
            ->assertJsonPath('websites.0.id', $website->id)
            ->assertJsonPath('websites.0.status', 'Online');
    }

    private function createWebsite(): Website
    {
        return Website::create([
            'nama_website' => 'Website Tes',
            'instansi' => 'Instansi Tes',
            'url' => 'https://example.test',
            'monitoring_aktif' => true,
        ]);
    }
}
