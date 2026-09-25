<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\MonitoringLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    /**
     * Cek satu website
     */
    public function check(Website $website)
    {
        $this->checkWebsite($website);

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
        $websites = Website::where('monitoring_aktif', true)->get();

        if ($websites->isEmpty()) {
            return redirect()
                ->route('dashboard')
                ->with(
                    'error',
                    'Belum ada website yang aktif untuk dimonitor.'
                );
        }

        foreach ($websites as $website) {
            $this->checkWebsite($website);
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Semua website berhasil diperiksa.'
            );
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
             */
            elseif (
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
             */
            else {

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