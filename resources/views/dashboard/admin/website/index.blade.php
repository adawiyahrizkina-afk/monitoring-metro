@extends('layouts.admin')

@section('title', 'Daftar Website')

@section('content')

<div class="page-header">

    <h1>Daftar Website</h1>
    <p>
        Daftar website yang terdaftar dalam sistem monitoring.
    </p>

</div>

<p id="websiteCheckStatus" class="website-check-status" aria-live="polite"></p>


<div class="card">

    <a href="{{ route('website.create') }}" class="btn">
        + Tambah Website
    </a>

    <input
        type="text"
        id="searchWebsite"
        class="search"
        placeholder="Cari instansi, OPD, atau URL..."
        onkeyup="searchTable()">

    <table id="websiteTable">

        <thead>

            <tr>
                <th>No</th>
                <th>Nama instansi</th>
                <th>OPD</th>
                <th>URL</th>
                <th>Grafik</th>
                <th>Status</th>
                <th style="text-align: center;">Interaksi</th>
            </tr>

        </thead>


        <body>

            @forelse($websites ?? [] as $website)

            <tr
                data-website-row
                data-website-id="{{ $website->id }}"
                data-check-url="{{ route('website.check', $website) }}"
                data-last-checked-at="{{ $website->last_checked_at?->toIso8601String() }}">

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                     {{ $website->nama_website ?? '-' }}
                </td>

                <td>
                     {{ $website->instansi ?? '-' }}
                </td>

                <td>
                    <a
                        href="{{ $website->url }}"
                        target="_blank"
                        class="url"
                        title="{{ $website->url }}">
                        {{ $website->url }}
                    </a>
                </td>

                <td class="website-mini-chart-cell">
                    <div class="website-mini-chart-wrap">
                        <a
                            href="{{ route('monitoring.index', ['website' => $website->id]) }}"
                            class="website-mini-chart-link"
                            aria-label="Buka grafik monitoring {{ $website->nama_website }}">
                            <canvas data-website-chart="{{ $website->id }}" role="img" aria-label="Grafik performa {{ $website->nama_website }}"></canvas>
                        </a>
                    </div>
                    <small data-recheck-timer aria-live="polite"></small>
                </td>

                <td data-field="status">

                    @if(($website->status ?? '') == 'Online')

                    <span class="status-online">
                        ● ONLINE
                    </span>

                    @elseif(($website->status ?? '') == 'Offline')

                    <span class="status-offline">
                        ● OFFLINE
                    </span>

                    @elseif(($website->status ?? '') == 'Warning')

                    <span class="status status-warning">
                        ● WARNING
                    </span>

                    @else

                    <span class="status-unchecked">
                        BELUM DICEK
                    </span>

                    @endif

                </td>

                <td class="button">
                    <a href="{{ route('website.edit', $website) }}"><button class="edit">Edit</button></a>
                    <form action="{{ route('website.destroy', $website) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus website ini? Riwayat monitoringnya juga akan terhapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete">Hapus</button>
                    </form>
                    <div class="website-check-control">
                        <button type="button" class="btn-action" data-check-website>Cek</button>
                    </div>
                </td>

            </tr>

            @empty

            <tr>

                <td colspan="6" style="text-align:center;">
                    Belum ada website yang terdaftar.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const websiteHistory = @json($chartHistory ?? []);
    const websiteCsrfToken = @json(csrf_token());
    const recheckIntervalMilliseconds = @json($websiteListIntervalSeconds * 1000);
    const chartLimit = 4;
    const chartColors = {
        Online: '#168c68',
        Warning: '#d47b16',
        Offline: '#c83b3b',
        'Belum Dicek': '#83918a'
    };
    const miniCharts = new Map();
    let automaticCheckRunning = false;

    function formatTimer(seconds) {
        return `${Math.floor(seconds / 60).toString().padStart(2, '0')}:${(seconds % 60).toString().padStart(2, '0')}`;
    }

    function updateMiniChart(websiteId) {
        const history = (websiteHistory[websiteId] || []).slice(-chartLimit);
        const chart = miniCharts.get(String(websiteId));
        if (!chart) return;

        chart.data.labels = history.map((entry) => entry.checked_at);
        chart.data.datasets[0].data = history.map((entry) => entry.score);
        chart.data.datasets[0].pointBackgroundColor = history.map((entry) => chartColors[entry.status] || chartColors['Belum Dicek']);
        chart.update('none');
    }

    document.querySelectorAll('[data-website-chart]').forEach((canvas) => {
        const websiteId = canvas.dataset.websiteChart;
        const history = (websiteHistory[websiteId] || []).slice(-chartLimit);
        websiteHistory[websiteId] = history;
        miniCharts.set(websiteId, new Chart(canvas, {
            type: 'line',
            data: {
                labels: history.map((entry) => entry.checked_at),
                datasets: [{
                    data: history.map((entry) => entry.score),
                    borderColor: '#168c68',
                    pointBackgroundColor: history.map((entry) => chartColors[entry.status] || chartColors['Belum Dicek']),
                    pointRadius: 2,
                    pointHoverRadius: 3,
                    borderWidth: 1.5,
                    tension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: (items) => items[0]?.label ? new Date(items[0].label).toLocaleString('id-ID') : '',
                            label: (item) => `Skor ${item.formattedValue}/100`
                        }
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false,
                        min: 0,
                        max: 100
                    }
                }
            }
        }));
    });

    function updateCountdown(row) {
        const button = row.querySelector('[data-check-website]');
        const timer = row.querySelector('[data-recheck-timer]');
        const remaining = Math.max(0, Math.ceil((Number(row.dataset.nextCheckAt || 0) - Date.now()) / 1000));
        button.disabled = row.dataset.checking === 'true';
        timer.textContent = row.dataset.checking === 'true' ?
            'Sedang diperiksa otomatis...' :
            remaining > 0 ?
            `Cek berikutnya dalam ${formatTimer(remaining)}` :
            'Pengecekan otomatis menunggu';
    }

    function startCountdown(row, checkedAt) {
        row.dataset.nextCheckAt = String(new Date(checkedAt).getTime() + recheckIntervalMilliseconds);
        updateCountdown(row);
    }

    document.querySelectorAll('[data-website-row]').forEach((row) => {
        if (row.dataset.lastCheckedAt) {
            startCountdown(row, row.dataset.lastCheckedAt);
        } else {
            row.dataset.nextCheckAt = String(Date.now());
            updateCountdown(row);
        }
    });

    async function checkWebsite(row, automatic = false) {
        if (row.dataset.checking === 'true') return;
        const statusCell = row.querySelector('[data-field="status"]');
        row.dataset.checking = 'true';
        const button = row.querySelector('[data-check-website]');
        button.disabled = true;
        button.textContent = automatic ? 'Otomatis...' : 'Memeriksa...';
        updateCountdown(row);

        try {
            const response = await fetch(row.dataset.checkUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': websiteCsrfToken
                }
            });
            if (!response.ok) throw new Error('Pemeriksaan website gagal.');

            const result = await response.json();
            const badge = statusCell.querySelector('.status-online, .status-offline, .status-warning, .status-unchecked');
            badge.classList.remove('status-online', 'status-offline', 'status-warning', 'status-unchecked');
            badge.classList.add({
                Online: 'status-online',
                Offline: 'status-offline',
                Warning: 'status-warning',
                'Belum Dicek': 'status-unchecked'
            } [result.status] || 'status-unchecked');
            badge.textContent = `● ${result.status.toUpperCase()}`;

            const history = websiteHistory[result.id] || (websiteHistory[result.id] = []);
            history.push({
                score: result.score,
                status: result.status,
                checked_at: result.last_checked_at
            });
            websiteHistory[result.id] = history.slice(-chartLimit);
            updateMiniChart(result.id);
            row.dataset.lastCheckedAt = result.last_checked_at || '';
            startCountdown(row, result.last_checked_at || new Date().toISOString());
            document.getElementById('websiteCheckStatus').textContent = automatic ?
                `${result.nama_website} diperiksa otomatis.` :
                `${result.nama_website} berhasil diperiksa.`;
        } catch (error) {
            document.getElementById('websiteCheckStatus').textContent = error.message;
            startCountdown(row, new Date().toISOString());
        } finally {
            delete row.dataset.checking;
            button.textContent = 'Cek';
            updateCountdown(row);
        }
    }

    async function checkNextDueWebsite() {
        if (automaticCheckRunning || document.hidden) return;

        const dueRow = Array.from(document.querySelectorAll('[data-website-row][data-next-check-at]'))
            .find((row) => Number(row.dataset.nextCheckAt) <= Date.now() && row.dataset.checking !== 'true');
        if (!dueRow) {
            document.querySelectorAll('[data-website-row][data-next-check-at]').forEach(updateCountdown);
            return;
        }

        automaticCheckRunning = true;
        try {
            await checkWebsite(dueRow, true);
        } finally {
            automaticCheckRunning = false;
        }
    }

    document.getElementById('websiteTable').addEventListener('click', (event) => {
        const button = event.target.closest('[data-check-website]');
        if (!button || button.disabled) return;
        checkWebsite(button.closest('[data-website-row]'));
    });

    window.setInterval(checkNextDueWebsite, 1000);
</script>

<script src="/js/search.js"></script>

@endsection