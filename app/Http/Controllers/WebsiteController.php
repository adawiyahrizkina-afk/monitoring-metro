<?php

namespace App\Http\Controllers;

use App\Models\MonitoringLog;
use App\Models\SystemSetting;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WebsiteController extends Controller
{
    public function dashboard()
    {
        $websites = Website::latest()->get();
        $total = $websites->count();
        $online = $websites->where('status', 'Online')->count();
        $offline = $websites->where('status', 'Offline')->count();
        $warning = $websites->where('status', 'Warning')->count();
        $unchecked = $websites->where('status', 'Belum Dicek')->count();
        $monitoringActive = $websites->where('monitoring_aktif', true)->count();
        return view('dashboard.admin.index', compact(
            'websites',
            'total',
            'online',
            'offline',
            'warning',
            'unchecked',
            'monitoringActive'
        ));
    }

    public function monitoring()
    {
        $websites = Website::latest()->get();
        $monitoringIntervalSeconds = (int) SystemSetting::getValue(
            SystemSetting::MONITORING_INTERVAL_KEY,
            (string) SystemSetting::DEFAULT_MONITORING_INTERVAL
        );
        if (! in_array($monitoringIntervalSeconds, SystemSetting::MONITORING_INTERVAL_OPTIONS, true)) {
            $monitoringIntervalSeconds = SystemSetting::DEFAULT_MONITORING_INTERVAL;
        }

        $chartHistory = $websites->mapWithKeys(function (Website $website) {
            return [$website->id => $website->monitoringLogs()
                ->latest('checked_at')
                ->limit(16)
                ->get()
                ->reverse()
                ->map(function (MonitoringLog $log) {
                    return [
                        'score' => $log->status === 'Offline' || $log->response_time === null
                            ? 0
                            : max(0, min(100, (int) round(100 - ($log->response_time / 50)))),
                        'status' => $log->status,
                        'checked_at' => $log->checked_at->toIso8601String(),
                    ];
                })
                ->values()];
        });

        return view('dashboard.admin.monitoring.index', compact(
            'websites',
            'chartHistory',
            'monitoringIntervalSeconds'
        ));
    }

    public function index()
    {
        $websites = Website::latest()->get();
        $websiteListIntervalSeconds = (int) SystemSetting::getValue(
            SystemSetting::WEBSITE_LIST_INTERVAL_KEY,
            (string) SystemSetting::DEFAULT_WEBSITE_LIST_INTERVAL
        );
        if (! in_array($websiteListIntervalSeconds, SystemSetting::WEBSITE_LIST_INTERVAL_OPTIONS, true)) {
            $websiteListIntervalSeconds = SystemSetting::DEFAULT_WEBSITE_LIST_INTERVAL;
        }

        $chartHistory = $websites->mapWithKeys(function (Website $website) {
            return [$website->id => $website->monitoringLogs()
                ->latest('checked_at')
                ->limit(4)
                ->get()
                ->reverse()
                ->map(function (MonitoringLog $log) {
                    return [
                        'score' => $log->status === 'Offline' || $log->response_time === null
                            ? 0
                            : max(0, min(100, (int) round(100 - ($log->response_time / 50)))),
                        'status' => $log->status,
                        'checked_at' => $log->checked_at->toIso8601String(),
                    ];
                })
                ->values()];
        });

        return view('dashboard.admin.website.index', compact(
            'websites',
            'chartHistory',
            'websiteListIntervalSeconds'
        ));
    }

    public function create()
    {
        return view('dashboard.admin.website.create');
    }

    public function edit(Website $website)
    {
        return view('dashboard.admin.website.edit', compact('website'));
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'nama_website' => 'required|string|max:100',
        'instansi'     => ['required', Rule::in(config('opd'))],
        'url'          => 'required|url|max:255',
    ]);

        Website::create([
            'nama_website' => $request->nama_website,
            'instansi' => $request->instansi,
            'url' => $request->url,
            'status' => 'Belum Dicek',
            'monitoring_aktif' => true,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Website berhasil ditambahkan.');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'extensions:csv'],
        ]);

        /** @var UploadedFile $file */
        $file = $validated['file'];
        $path = $file->getRealPath();
        if ($path === false) {
            throw new \RuntimeException('File CSV tidak dapat dibaca.');
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException('File CSV tidak dapat dibaca.');
        }

        try {
            $headers = fgetcsv($handle, 0, ',', '"', '\\');
            if ($headers === false) {
                return back()->withErrors(['file' => 'File CSV kosong atau tidak memiliki header.']);
            }

            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
            $headers = array_map(fn ($header) => strtolower(trim((string) $header)), $headers);
            $domainColumn = array_search('link / subdomain', $headers, true);
            $agencyColumn = array_search('kategori / instansi', $headers, true);

            if ($domainColumn === false || $agencyColumn === false) {
                return back()->withErrors([
                    'file' => 'Header CSV harus memuat kolom "Link / Subdomain" dan "Kategori / Instansi".',
                ]);
            }

            $rows = [];
            while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                if ($row === [null] || $row === []) {
                    continue;
                }

                $rows[] = [
                    'domain' => trim((string) ($row[$domainColumn] ?? '')),
                    'agency' => trim((string) ($row[$agencyColumn] ?? '')),
                ];
            }
        } finally {
            fclose($handle);
        }

        $knownHosts = Website::query()
            ->pluck('url')
            ->mapWithKeys(fn (string $url) => [$this->websiteHost($url) => true])
            ->all();
        $added = 0;
        $duplicates = 0;
        $missingAgency = 0;
        $invalidRows = 0;

        DB::transaction(function () use (
            $rows,
            &$knownHosts,
            &$added,
            &$duplicates,
            &$missingAgency,
            &$invalidRows
        ) {
            foreach ($rows as $row) {
                if ($row['agency'] === '') {
                    $missingAgency++;
                    continue;
                }

                if (strlen($row['agency']) > 100) {
                    $invalidRows++;
                    continue;
                }

                $host = $this->websiteHost($row['domain']);
                if ($host === null) {
                    $invalidRows++;
                    continue;
                }

                if (isset($knownHosts[$host])) {
                    $duplicates++;
                    continue;
                }

                Website::create([
                    'nama_website' => explode('.', $host)[0],
                    'instansi' => $row['agency'],
                    'url' => 'https://' . $host,
                    'status' => 'Belum Dicek',
                    'monitoring_aktif' => true,
                ]);

                $knownHosts[$host] = true;
                $added++;
            }
        });

        $details = [];
        if ($duplicates > 0) {
            $details[] = "{$duplicates} duplikat dilewati";
        }
        if ($missingAgency > 0) {
            $details[] = "{$missingAgency} baris tanpa instansi dilewati";
        }
        if ($invalidRows > 0) {
            $details[] = "{$invalidRows} baris dengan data tidak valid dilewati";
        }

        $message = "Import selesai: {$added} website ditambahkan.";
        if ($details !== []) {
            $message .= ' ' . implode(', ', $details) . '.';
        }

        return redirect()
            ->route('website.index')
            ->with($added > 0 ? 'success' : 'warning', $message);
    }

    private function websiteHost(string $url): ?string
    {
        $candidate = preg_match('/^https?:\/\//i', $url) ? $url : 'https://' . $url;
        $parts = parse_url($candidate);
        $host = strtolower(rtrim((string) ($parts['host'] ?? ''), '.'));

        if ($host === '' || filter_var('https://' . $host, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $host;
    }

    public function update(Request $request, Website $website)
    {
        $validated = $request->validate([
            'nama_website' => 'required|string|max:100',
            'instansi' => 'required|string|max:100',
            'url' => 'required|url|max:255',
        ]);

        $website->update($validated);

        return redirect()
            ->route('website.index')
            ->with('success', 'Website berhasil diperbarui.');
    }

    public function destroy(Website $website)
    {
        $website->delete();

        return redirect()
            ->route('website.index')
            ->with('success', 'Website berhasil dihapus.');
    }
}
