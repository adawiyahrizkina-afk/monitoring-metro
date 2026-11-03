<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WebsiteHistoryLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_website_list_loads_monitoring_history_with_a_single_query(): void
    {
        $this->createWebsites();

        $this->assertHistoryQueryCount('website.index');
    }

    public function test_monitoring_page_loads_monitoring_history_with_a_single_query(): void
    {
        $this->createWebsites();

        $this->assertHistoryQueryCount('monitoring.index');
    }

    private function createWebsites(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
        ]);

        foreach (range(1, 3) as $number) {
            Website::create([
                'nama_website' => "Website Tes {$number}",
                'instansi' => 'Instansi Tes',
                'url' => "https://example{$number}.test",
            ]);
        }
    }

    private function assertHistoryQueryCount(string $routeName): void
    {
        $historyQueryCount = 0;
        DB::listen(function (QueryExecuted $query) use (&$historyQueryCount) {
            if (str_contains($query->sql, 'monitoring_logs')) {
                $historyQueryCount++;
            }
        });

        $this->actingAs(User::first())
            ->get(route($routeName))
            ->assertOk();

        $this->assertSame(1, $historyQueryCount);
    }
}
