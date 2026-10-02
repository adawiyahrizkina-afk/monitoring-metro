@extends('layouts.admin')

@section('title', 'Monitoring Website')

@section('content')


<div class="page-header">
    <div>
        <h1>Monitoring Website</h1>
        <p>Pantau kondisi website Pemerintah Kota Metro.</p>
    </div>
    <form action="{{ route('monitoring.realtime') }}" method="POST" id="realtimeCheckForm">
        @csrf
        <button type="submit" class="btn" id="realtimeCheckButton">Cek sekarang</button>
    </form>
</div>

<p class="realtime-status" id="realtimeStatus" aria-live="polite">Pengecekan otomatis aktif. Memeriksa website...</p>

<section class="monitoring-panel monitoring-chart-panel" aria-labelledby="historyTitle">
    <div class="monitoring-panel-heading">
        <div>
            <h2 id="historyTitle">Riwayat performa</h2>
            <p id="chartWebsiteInfo">Pilih website untuk melihat riwayatnya.</p>
        </div>
        <div class="chart-website-controls">
            <label class="chart-select-label" for="chartWebsiteSearch">Cari website
                <input type="search" id="chartWebsiteSearch" placeholder="Ketik nama website">
            </label>
            <label class="chart-select-label" for="chartWebsiteSelect">Website
                <select id="chartWebsiteSelect">
                    @foreach($websites ?? [] as $website)
                    <option value="{{ $website->id }}" data-search="{{ strtolower($website->nama_website . ' ' . $website->instansi) }}">{{ $website->nama_website }} · {{ $website->instansi }}</option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>
    <div class="chart-legend" aria-label="Keterangan grafik">
        <span><i class="legend-online"></i>Normal</span>
        <span><i class="legend-warning"></i>Lambat</span>
        <span><i class="legend-offline"></i>Down</span>
    </div>
    <div class="monitoring-chart-canvas-wrap">
        <canvas id="websitePerformanceChart" role="img" aria-label="Grafik perubahan skor performa berdasarkan waktu"></canvas>
    </div>
    <div class="chart-events" id="chartEvents" aria-live="polite"></div>
</section>

<section class="monitoring-panel" aria-labelledby="websiteListTitle">
    <div class="monitoring-panel-heading">
        <div>
            <h2 id="websiteListTitle">Daftar website</h2>
            <p id="websiteResultCount">Memuat daftar...</p>
        </div>
        <div class="monitoring-filters">
            <label class="monitoring-search-label" for="searchWebsite">Cari</label>
            <input type="search" id="searchWebsite" placeholder="Nama, instansi, atau URL">
            <label class="monitoring-search-label" for="statusFilter">Status</label>
            <select id="statusFilter">
                <option value="">Semua status</option>
                <option value="Offline">Down</option>
                <option value="Warning">Lambat</option>
                <option value="Online">Online</option>
                <option value="Belum Dicek">Belum dicek</option>
            </select>
        </div>
    </div>

    <div class="monitoring-table-wrap">
        <table class="monitoring-table">
            <thead>
                <tr>
                    <th scope="col">Website / instansi</th>
                    <th scope="col">Status</th>
                    <th scope="col">Respons</th>
                    <th scope="col">Terakhir dicek</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody id="websiteTable">
                @forelse($websites ?? [] as $website)
                <tr
                    data-website-id="{{ $website->id }}"
                    data-website-name="{{ $website->nama_website }}"
                    data-instansi="{{ $website->instansi }}"
                    data-check-url="{{ route('website.check', $website) }}"
                    tabindex="0"
                    aria-label="Pilih {{ $website->nama_website }} untuk melihat riwayat dan memeriksa status">
                    <td>
                        <strong>{{ $website->nama_website }}</strong>
                        <span>{{ $website->instansi }}</span>
                    </td>
                    <td data-field="status" data-status="{{ $website->status }}">{{ $website->status }}</td>
                    <td data-field="response">
                        {{ $website->response_time === null ? '-' : number_format($website->response_time / 1000, 2, ',', '.') . ' detik' }}
                    </td>
                    <td data-field="checked-at">{{ $website->last_checked_at?->format('d/m/Y H:i:s') ?? '-' }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="checkWebsite({{ $website->id }})">Periksa</button>
                        <a href="{{ route('riwayat.index') }}"><button class="btn btn-sm btn-primary">Detail</button></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">Belum ada website yang terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <p class="monitoring-empty" id="websiteEmptyState" hidden>Tidak ada website yang cocok dengan pencarian ini.</p>
    </div>

    <div class="monitoring-pagination">
        <label for="pageSize">Baris per halaman</label>
        <select id="pageSize">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span id="pageStatus">Halaman 1 dari 1</span>
        <button type="button" id="previousPage" aria-label="Halaman sebelumnya">Sebelumnya</button>
        <button type="button" id="nextPage" aria-label="Halaman berikutnya">Berikutnya</button>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const historyByWebsite = @json($chartHistory ?? []);
    const chartSelect = document.getElementById('chartWebsiteSelect');
    const chartSearch = document.getElementById('chartWebsiteSearch');
    const chartInfo = document.getElementById('chartWebsiteInfo');
    const chartEvents = document.getElementById('chartEvents');
    const websiteRows = document.getElementById('websiteTable');
    const realtimeForm = document.getElementById('realtimeCheckForm');
    const realtimeButton = document.getElementById('realtimeCheckButton');
    const realtimeStatus = document.getElementById('realtimeStatus');
    const originalOptions = Array.from(chartSelect.options).map((option) => option.cloneNode(true));
    const chartHistoryLimit = 16;
    let isChecking = false;
    let refreshTimer = null;

    Object.keys(historyByWebsite).forEach((websiteId) => {
        historyByWebsite[websiteId] = historyByWebsite[websiteId].slice(-chartHistoryLimit);
    });
    const statusColors = {
        Online: '#168c68',
        Warning: '#d47b16',
        Offline: '#c83b3b',
        'Belum Dicek': '#83918a'
    };

    const performanceChart = new Chart(document.getElementById('websitePerformanceChart'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Skor performa',
                data: [],
                borderColor: '#168c68',
                pointBackgroundColor: [],
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 2,
                tension: 0.25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Waktu pemeriksaan'
                    }
                },
                y: {
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Skor performa'
                    }
                }
            }
        }
    });

    function formatCheckTime(value) {
        if (!value) return '--:--';
        const date = new Date(value);
        return Number.isNaN(date.getTime()) ? '--:--' : date.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    function renderEvents(history) {
        const events = history.filter((entry) => entry.status === 'Warning' || entry.status === 'Offline').slice().reverse();
        chartEvents.replaceChildren();
        if (!events.length) {
            chartEvents.textContent = 'Tidak ada gangguan pada riwayat yang ditampilkan.';
            return;
        }

        events.forEach((entry) => {
            const event = document.createElement('span');
            event.className = entry.status === 'Offline' ? 'chart-event is-offline' : 'chart-event is-warning';
            event.textContent = `${entry.status === 'Offline' ? 'DOWN' : 'LAMBAT'} ${formatCheckTime(entry.checked_at)}`;
            chartEvents.appendChild(event);
        });
    }

    function updateChart(websiteId) {
        const selectedOption = Array.from(chartSelect.options).find((option) => option.value === String(websiteId));
        const row = websiteRows.querySelector(`[data-website-id="${websiteId}"]`);
        const history = historyByWebsite[websiteId] || [];
        const websiteName = row?.dataset.websiteName || selectedOption?.textContent || 'Website';
        const institution = row?.dataset.instansi;

        chartInfo.textContent = institution ? `${websiteName} · ${institution}` : websiteName;
        performanceChart.data.labels = history.map((entry) => formatCheckTime(entry.checked_at));
        performanceChart.data.datasets[0].label = `Skor performa · ${websiteName}`;
        performanceChart.data.datasets[0].data = history.map((entry) => entry.score);
        performanceChart.data.datasets[0].pointBackgroundColor = history.map((entry) => statusColors[entry.status] || statusColors['Belum Dicek']);
        performanceChart.update();
        renderEvents(history);
    }

    function filterChartOptions() {
        const query = chartSearch.value.trim().toLocaleLowerCase('id-ID');
        const matches = originalOptions.filter((option) => (option.dataset.search || option.textContent.toLocaleLowerCase('id-ID')).includes(query));
        const previousValue = chartSelect.value;
        chartSelect.replaceChildren(...matches);

        if (!matches.length) {
            const emptyOption = new Option('Tidak ada website yang cocok', '');
            emptyOption.disabled = true;
            chartSelect.add(emptyOption);
            chartInfo.textContent = 'Tidak ada website yang cocok dengan pencarian.';
            return;
        }

        chartSelect.value = matches.some((option) => option.value === previousValue) ? previousValue : matches[0].value;
        updateChart(chartSelect.value);
    }

    function selectWebsite(websiteId) {
        if (!originalOptions.some((option) => option.value === String(websiteId))) return;
        if (!Array.from(chartSelect.options).some((option) => option.value === String(websiteId))) {
            chartSearch.value = '';
            filterChartOptions();
        }
        chartSelect.value = String(websiteId);
        updateChart(websiteId);
    }

    async function checkWebsite(row) {
        if (isChecking || row.dataset.checking === 'true') return;
        isChecking = true;
        row.dataset.checking = 'true';
        const statusCell = row.querySelector('[data-field="status"]');
        const responseCell = row.querySelector('[data-field="response"]');
        const checkedAtCell = row.querySelector('[data-field="checked-at"]');
        statusCell.textContent = 'Memeriksa...';

        try {
            const response = await fetch(row.dataset.checkUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                }
            });
            if (!response.ok) throw new Error('Gagal memeriksa website.');

            const result = await response.json();
            statusCell.textContent = result.status;
            statusCell.dataset.status = result.status;
            responseCell.textContent = result.response_time === null ? '-' : `${(result.response_time / 1000).toFixed(2).replace('.', ',')} detik`;
            checkedAtCell.textContent = result.last_checked_at ? new Date(result.last_checked_at).toLocaleString('id-ID') : '-';

            const history = historyByWebsite[result.id] || (historyByWebsite[result.id] = []);
            history.push({
                score: result.score,
                status: result.status,
                checked_at: result.last_checked_at
            });
            historyByWebsite[result.id] = history.slice(-chartHistoryLimit);
            if (chartSelect.value === String(result.id)) updateChart(result.id);
            realtimeStatus.textContent = `${result.nama_website} diperiksa pada ${formatCheckTime(result.last_checked_at)}.`;
        } catch (error) {
            statusCell.textContent = 'Gagal diperiksa';
            realtimeStatus.textContent = error.message;
        } finally {
            delete row.dataset.checking;
            isChecking = false;
        }
    }

    function updateWebsiteResult(result) {
        const row = websiteRows.querySelector(`[data-website-id="${result.id}"]`);
        if (!row) return;

        const statusCell = row.querySelector('[data-field="status"]');
        statusCell.textContent = result.status;
        statusCell.dataset.status = result.status;
        row.querySelector('[data-field="response"]').textContent = result.response_time === null ?
            '-' :
            `${(result.response_time / 1000).toFixed(2).replace('.', ',')} detik`;
        row.querySelector('[data-field="checked-at"]').textContent = result.last_checked_at ?
            new Date(result.last_checked_at).toLocaleString('id-ID') :
            '-';

        const history = historyByWebsite[result.id] || (historyByWebsite[result.id] = []);
        history.push({
            score: result.score,
            status: result.status,
            checked_at: result.last_checked_at
        });
        historyByWebsite[result.id] = history.slice(-chartHistoryLimit);
    }

    async function refreshMonitoring() {
        if (isChecking || document.hidden) return;
        isChecking = true;
        realtimeButton.disabled = true;
        realtimeStatus.textContent = 'Sedang memeriksa website aktif...';

        try {
            const response = await fetch(realtimeForm.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                }
            });
            if (!response.ok) throw new Error('Gagal memperbarui data monitoring.');

            const data = await response.json();
            data.websites.forEach(updateWebsiteResult);
            if (chartSelect.value) updateChart(chartSelect.value);
            realtimeStatus.textContent = `Pembaruan terakhir ${formatCheckTime(data.checked_at)}. Pengecekan otomatis setiap 30 detik.`;
        } catch (error) {
            realtimeStatus.textContent = error.message;
        } finally {
            isChecking = false;
            realtimeButton.disabled = false;
        }
    }

    function scheduleNextRefresh() {
        window.clearInterval(refreshTimer);
        if (!document.hidden) refreshTimer = window.setInterval(refreshMonitoring, 30000);
    }

    realtimeForm.addEventListener('submit', (event) => {
        event.preventDefault();
        refreshMonitoring();
    });

    document.addEventListener('visibilitychange', () => {
        window.clearInterval(refreshTimer);
        if (document.hidden) return;
        refreshMonitoring();
        scheduleNextRefresh();
    });

    chartSearch.addEventListener('input', filterChartOptions);
    chartSelect.addEventListener('change', () => updateChart(chartSelect.value));
    websiteRows.addEventListener('click', (event) => {
        const row = event.target.closest('tr[data-website-id]');
        if (!row) return;
        selectWebsite(row.dataset.websiteId);
        checkWebsite(row);
    });
    websiteRows.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const row = event.target.closest('tr[data-website-id]');
        if (!row) return;
        event.preventDefault();
        selectWebsite(row.dataset.websiteId);
        checkWebsite(row);
    });

    if (chartSelect.value) updateChart(chartSelect.value);
    refreshMonitoring();
    scheduleNextRefresh();
</script>

@endsection