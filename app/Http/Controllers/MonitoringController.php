<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Models\MonitoringLog;
use Illuminate\Support\Facades\Http;

class MonitoringController extends Controller
{
    public function check(Website $website)
    {
        $this->checkWebsite($website);

        return back()->with(
            'success',
            $website->nama_website .
            ' berhasil diperiksa.'
        );
    }


    public function checkAll()
    {
        $websites = Website::where(
            'monitoring_aktif',
            true
        )->get();

        foreach ($websites as $website) {

            $this->checkWebsite($website);
        }

        return back()->with(
            'success',
            'Semua website berhasil diperiksa.'
        );
    }


    private function checkWebsite(Website $website)
    {
        $start = microtime(true);

        try {

            $response = Http::timeout(10)
                ->get($website->url);

            $responseTime = round(
                (microtime(true) - $start) * 1000
            );

            /*
             * Status 200 sampai 399
             * dianggap website masih dapat diakses.
             */

            if (
                $response->status() >= 200 &&
                $response->status() < 400
            ) {

                $status = 'Online';

                $keterangan =
                    'Website dapat diakses.';

            } else {

                $status = 'Offline';

                $keterangan =
                    'Website memberikan HTTP status ' .
                    $response->status();
            }

        } catch (\Exception $e) {

            $status = 'Offline';

            $responseTime = null;

            $keterangan =
                'Website tidak dapat diakses.';
        }


        /*
         * Update website
         */

        $website->update([

            'status' => $status,

            'response_time' => $responseTime,

            'last_checked_at' => now(),

        ]);


        /*
         * Simpan riwayat
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