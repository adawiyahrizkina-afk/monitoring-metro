<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WebsiteImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_creates_websites_and_skips_invalid_or_duplicate_rows(): void
    {
        Website::create([
            'nama_website' => 'existing',
            'instansi' => 'Dinas Kesehatan',
            'url' => 'https://existing.metrokota.go.id',
        ]);

        $csv = "\xEF\xBB\xBFLink / Subdomain,Kategori / Instansi\n"
            ."metrobarat.metrokota.go.id,Kecamatan Metro Barat\n"
            ."layanan-online.metrokota.go.id,Dinas Kependudukan dan Pencatatan Sipil\n"
            ."existing.metrokota.go.id,Pemerintah Kota Metro\n"
            ."no-agency.metrokota.go.id,\n"
            ."not a domain,Dinas Kesehatan\n"
            ."metrobarat.metrokota.go.id,Kecamatan Metro Barat\n";

        $response = $this->actingAs($this->admin())
            ->post(route('website.import'), [
                'file' => UploadedFile::fake()->createWithContent('websites.csv', $csv),
            ]);

        $response->assertRedirect(route('website.index'))
            ->assertSessionHas('success', 'Import selesai: 2 website ditambahkan. 2 duplikat dilewati, 1 baris tanpa instansi dilewati, 1 baris dengan data tidak valid dilewati.');

        $this->assertDatabaseHas('websites', [
            'nama_website' => 'metrobarat',
            'instansi' => 'Kecamatan Metro Barat',
            'url' => 'https://metrobarat.metrokota.go.id',
            'status' => 'Belum Dicek',
            'monitoring_aktif' => true,
        ]);
        $this->assertDatabaseHas('websites', [
            'nama_website' => 'layanan-online',
            'url' => 'https://layanan-online.metrokota.go.id',
        ]);
        $this->assertDatabaseCount('websites', 3);
    }

    public function test_csv_import_requires_expected_headers(): void
    {
        $response = $this->actingAs($this->admin())
            ->post(route('website.import'), [
                'file' => UploadedFile::fake()->createWithContent(
                    'websites.csv',
                    "domain,agency\nexample.metrokota.go.id,Dinas Kesehatan\n"
                ),
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('websites', 0);
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
