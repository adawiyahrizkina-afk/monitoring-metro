<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\MonitoringLog;
use App\Services\MonitoringLogCleanup;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class MonitoringController extends Controller
{
    /**
     * Cek satu website
     */
    public function check(\Illuminate\Http\Request $request, Website $website)
    {
        app(MonitoringLogCleanup::class)->handle();
        $this->checkWebsite($website);

        if ($request->expectsJson()) {
            $website->refresh();

            return response()->json([
                'id' => $website->id,
                'nama_website' => $website->nama_website,
                'url' => $website->url,
                'status' => $website->status,
                'response_time' => $website->response_time,
                'last_checked_at' => $website->last_checked_at?->toIso8601String(),
                'score' => $this->performanceScore($website->response_time, $website->status),
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                $website->nama_website . ' berhasil diperiksa.'
            );
    }

    /**
     * Cek semua website yang monitoringnya aktif
     */
    public function checkAll()
    {
        $websites = $this->checkAllWebsites();

        if ($websites->isEmpty()) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Belum ada website yang aktif untuk dimonitor.'
                );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Semua website berhasil diperiksa.'
            );
    }

    public function checkAllRealtime(): JsonResponse
    {
        $websites = $this->checkAllWebsites();

        return response()->json([
            'websites' => $websites->map(function (Website $website) {
                return [
                    'id' => $website->id,
                    'nama_website' => $website->nama_website,
                    'url' => $website->url,
                    'status' => $website->status,
                    'response_time' => $website->response_time,
                    'last_checked_at' => $website->last_checked_at?->toIso8601String(),
                    'score' => $this->performanceScore($website->response_time, $website->status),
                ];
            })->values(),
            'checked_at' => now()->toIso8601String(),
        ]);
    }

    public function destroyAllLogs()
    {
        $deletedCount = MonitoringLog::query()->delete();

        return redirect()
            ->route('riwayat.index')
            ->with(
                'success',
                $deletedCount > 0
                    ? "Berhasil menghapus {$deletedCount} log monitoring."
                    : 'Tidak ada log monitoring untuk dihapus.'
            );
    }

    private function checkAllWebsites()
    {
        app(MonitoringLogCleanup::class)->handle();
        $websites = Website::where('monitoring_aktif', true)->get();

        foreach ($websites as $website) {
            $this->checkWebsite($website);
        }

        return $websites->fresh();
    }

    private function performanceScore(?int $responseTime, string $status): int
    {
        if ($status === 'Offline' || $responseTime === null) {
            return 0;
        }

        return max(0, min(100, (int) round(100 - ($responseTime / 50))));
    }

    /**
     * Proses pengecekan website
     */
    private function checkWebsite(Website $website)
    {
        $start = microtime(true);

        $status = 'Offline';
        $keterangan = 'Website tidak dapat diakses.';
        $responseTime = null;

        try {

            /*
             * Mengirim request GET ke website.
             *
             * timeout(10)
             * = maksimal menunggu 10 detik.
             *
             * connectTimeout(5)
             * = maksimal 5 detik untuk membuat koneksi.
             */
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'User-Agent' => 'Monitoring-Website-Kota-Metro/1.0'
                ])
                ->withOptions([
                    'allow_redirects' => true,
                    'verify' => true,
                ])
                ->get($website->url);

            /*
             * Hitung waktu respons dalam milidetik.
             */
            $responseTime = round(
                (microtime(true) - $start) * 1000
            );

            $httpStatus = $response->status();

            /*
             * STATUS ONLINE
             *
             * HTTP 200 sampai 399
             * dan response kurang dari atau sama dengan 1000 ms.
             */
            if (
                $httpStatus >= 200 &&
                $httpStatus < 400 &&
                $responseTime <= 1000
            ) {
                $status = 'Online';
                $keterangan =
                    'Website dapat diakses dengan normal. ' .
                    'HTTP ' . $httpStatus . '.';
            }

            /*
             * STATUS WARNING
             *
             * Website masih bisa diakses,
             * tetapi response lebih dari 1000 ms.
             */ elseif (
                $httpStatus >= 200 &&
                $httpStatus < 400 &&
                $responseTime > 1000
            ) {

                $status = 'Warning';

                $keterangan =
                    'Website dapat diakses tetapi respons lambat. ' .
                    'HTTP ' . $httpStatus . '.';
            }

            /*
             * STATUS OFFLINE
             *
             * HTTP 400 atau lebih.
             */ else {

                $status = 'Offline';
                $keterangan =
                    'Website memberikan HTTP status ' .
                    $httpStatus . '.';
            }
        } catch (\Throwable $e) {

            /*
             * Kalau koneksi gagal, timeout,
             * DNS tidak ditemukan, SSL bermasalah,
             * atau error lainnya.
             */
            $responseTime = round(
                (microtime(true) - $start) * 1000
            );

            $status = 'Offline';

            $keterangan =
                'Website tidak dapat diakses. ' .
                'Koneksi gagal atau timeout.';

            Log::error(
                'Monitoring website gagal',
                [
                    'website' => $website->url,
                    'error' => $e->getMessage(),
                ]
            );
        }

        /*
         * Update kondisi website terakhir.
         */
        $website->update([
            'status' => $status,
            'response_time' => $responseTime,
            'last_checked_at' => now(),
        ]);

        /*
         * Simpan riwayat monitoring.
         */
        MonitoringLog::create([
            'website_id' => $website->id,
            'status' => $status,
            'response_time' => $responseTime,
            'checked_at' => now(),
            'keterangan' => $keterangan,
        ]);
    }
}
