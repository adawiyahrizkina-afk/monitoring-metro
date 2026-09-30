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
        <button type="submit" class="btn" id="realtimeCheckButton">Cek semua website</button>
    </form>
</div>

<p class="realtime-status" id="realtimeStatus" aria-live="polite">Memuat data monitoring...</p>

<section class="monitoring-summary" aria-label="Ringkasan status website">
    @foreach(['Offline' => 'Down', 'Warning' => 'Lambat', 'Online' => 'Online', 'Belum Dicek' => 'Belum dicek'] as $status => $label)
    <button class="monitoring-summary-item" type="button" data-status-filter="{{ $status }}">
        <span>{{ $label }}</span>
        <strong data-summary-status="{{ $status }}">0</strong>
    </button>
    @endforeach
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
                </tr>
            </thead>
            <tbody id="websiteTable"></tbody>
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

<section class="monitoring-panel monitoring-chart-panel" aria-labelledby="historyTitle">
    <div class="monitoring-panel-heading">
        <div>
            <h2 id="historyTitle">Riwayat performa</h2>
            <p id="chartWebsiteInfo">Pilih website untuk melihat riwayatnya.</p>
        </div>
        <label class="chart-select-label" for="chartWebsiteSelect">Website
            <select id="chartWebsiteSelect">
                @foreach($websites ?? [] as $website)
                <option value="{{ $website->id }}">{{ $website->nama_website }} · {{ $website->instansi }}</option>
                @endforeach
            </select>
        </label>
    </div>
    <div class="chart-legend" aria-label="Keterangan grafik">
        <span><i class="legend-online"></i>Normal</span>
        <span><i class="legend-warning"></i>Lambat</span>
        <span><i class="legend-offline"></i>Down</span>
    </div>
    <canvas id="websitePerformanceChart" height="220" role="img" aria-label="Grafik perubahan skor performa berdasarkan waktu"></canvas>
    <div class="chart-events" id="chartEvents" aria-live="polite"></div>
</section>

<script>
    const realtimeUrl = @json(route('monitoring.realtime'));
    const csrfToken = @json(csrf_token());
    const initialWebsites = @json($websites ?? []);
    const initialHistory = @json($chartHistory ?? []);
    const statusText = document.getElementById('realtimeStatus');
    const checkForm = document.getElementById('realtimeCheckForm');
    const checkButton = document.getElementById('realtimeCheckButton');
    const searchInput = document.getElementById('searchWebsite');
    const statusFilter = document.getElementById('statusFilter');
    const pageSizeInput = document.getElementById('pageSize');
    const tableBody = document.getElementById('websiteTable');
    const performanceCanvas = document.getElementById('websitePerformanceChart');
    const chartSelector = document.getElementById('chartWebsiteSelect');
    const performanceHistory = {};
    let websites = initialWebsites;
    let currentPage = 1;
    let isRefreshing = false;

    function formatSeconds(milliseconds) {
        return milliseconds === null ? '-' : `${(milliseconds / 1000).toFixed(2).replace('.', ',')} detik`;
    }

    function calculateScore(website) {
        if (website.status === 'Offline' || website.response_time === null) return 0;
        return Math.max(0, Math.min(100, Math.round(100 - (website.response_time / 50))));
    }

    function formatMonitoringTime(value) {
        if (!value) return '--:--';
        return new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(value));
    }

    function formatMonitoringDateTime(value) {
        if (!value) return '--';
        return new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(value));
    }

    function drawWebsiteChart() {
        const websiteId = chartSelector.value;
        const chartContext = performanceCanvas.getContext('2d');
        const width = performanceCanvas.clientWidth || 600;
        const height = 220;
        const deviceRatio = window.devicePixelRatio || 1;
        performanceCanvas.width = width * deviceRatio;
        performanceCanvas.height = height * deviceRatio;
        chartContext.setTransform(deviceRatio, 0, 0, deviceRatio, 0, 0);
        chartContext.clearRect(0, 0, width, height);

        const selectedWebsite = websites.find(function(website) {
            return String(website.id) === String(websiteId);
        });
        const chartInfo = document.getElementById('chartWebsiteInfo');
        chartInfo.textContent = selectedWebsite ? `${selectedWebsite.nama_website} · ${selectedWebsite.instansi} · ${selectedWebsite.url}` : 'Belum ada website untuk ditampilkan.';

        chartContext.strokeStyle = '#e4e8e5';
        chartContext.fillStyle = '#58645f';
        chartContext.font = '12px Arial';
        [0, 25, 50, 75, 100].forEach(function(value) {
            const y = height - 38 - (value / 100) * (height - 60);
            chartContext.beginPath();
            chartContext.moveTo(48, y);
            chartContext.lineTo(width - 12, y);
            chartContext.stroke();
            chartContext.fillText(String(value), 14, y + 4);
        });

        const history = performanceHistory[websiteId] || [];
        if (!history.length) {
            chartContext.fillText('Belum ada riwayat monitoring untuk website ini.', 58, height / 2);
            return;
        }

        const timestamps = history.map(function(entry) {
            return new Date(entry.checked_at).getTime();
        });
        const firstTimestamp = timestamps[0];
        const timeRange = timestamps[timestamps.length - 1] - firstTimestamp;
        const plotWidth = Math.max(width - 68, 1);
        const points = history.map(function(entry, index) {
            const x = 52 + (timeRange > 0 ? ((timestamps[index] - firstTimestamp) / timeRange) * plotWidth :
                (history.length === 1 ? 0 : (index / (history.length - 1)) * plotWidth));
            const y = height - 38 - (entry.score / 100) * (height - 58);
            return {
                x,
                y,
                ...entry
            };
        });

        chartContext.strokeStyle = '#13795b';
        chartContext.lineWidth = 2.5;
        chartContext.beginPath();
        points.forEach(function(point, index) {
            index === 0 ?
                chartContext.moveTo(point.x, point.y) :
                chartContext.lineTo(point.x, point.y);
        });
        chartContext.stroke();

        points.forEach(function(point, index) {
            chartContext.fillStyle = point.status === 'Offline' ? '#c83b3b' :
                point.status === 'Warning' ? '#d47b16' : '#168c68';
            chartContext.beginPath();
            chartContext.arc(point.x, point.y, 5, 0, Math.PI * 2);
            chartContext.fill();

            if (index % Math.max(1, Math.ceil(points.length / 6)) === 0 || index === points.length - 1) {
                chartContext.fillStyle = '#55708c';
                chartContext.textAlign = 'center';
                chartContext.fillText(formatMonitoringTime(point.checked_at), point.x, height - 8);
            }
        });
        chartContext.textAlign = 'start';
        renderChartEvents(history);
    }

    function updateCharts(websites, checkedAt) {
        websites.forEach(function(website) {
            if (!performanceHistory[website.id]) performanceHistory[website.id] = [];
            performanceHistory[website.id].push({
                score: calculateScore(website),
                status: website.status,
                checked_at: website.last_checked_at || checkedAt
            });
            performanceHistory[website.id] = performanceHistory[website.id].slice(-24);
        });
        drawWebsiteChart();
    }

    function renderChartEvents(history) {
        const eventList = document.getElementById('chartEvents');
        const events = history.filter(function(entry) {
            return entry.status === 'Warning' || entry.status === 'Offline';
        }).slice(-8).reverse();

        eventList.replaceChildren();
        if (!events.length) {
            eventList.textContent = 'Tidak ada gangguan pada riwayat yang ditampilkan.';
            return;
        }

        events.forEach(function(entry) {
            const event = document.createElement('span');
            event.className = entry.status === 'Offline' ? 'chart-event is-offline' : 'chart-event is-warning';
            event.textContent = `${entry.status === 'Offline' ? 'DOWN' : 'LAMBAT'} · ${formatMonitoringDateTime(entry.checked_at)}`;
            eventList.appendChild(event);
        });
    }

    function getStatusClass(status) {
        return {
            Online: 'status-online',
            Offline: 'status-offline',
            Warning: 'status-warning',
            'Belum Dicek': 'status-unchecked'
        } [status] || 'status-unchecked';
    }

    function updateSummary() {
        ['Online', 'Offline', 'Warning', 'Belum Dicek'].forEach(function(status) {
            const count = websites.filter(function(website) {
                return website.status === status;
            }).length;
            document.querySelector(`[data-summary-status="${status}"]`).textContent = count;
        });
    }

    function filteredWebsites() {
        const keyword = searchInput.value.trim().toLocaleLowerCase('id');
        const status = statusFilter.value;
        return websites.filter(function(website) {
            const searchable = `${website.nama_website} ${website.instansi} ${website.url}`.toLocaleLowerCase('id');
            return searchable.includes(keyword) && (!status || website.status === status);
        });
    }

    function appendCell(row, text, className) {
        const cell = document.createElement('td');
        if (className) cell.className = className;
        cell.textContent = text;
        row.appendChild(cell);
        return cell;
    }

    function renderWebsiteTable() {
        const filtered = filteredWebsites();
        const pageSize = Number(pageSizeInput.value);
        const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
        currentPage = Math.min(currentPage, pageCount);
        const firstIndex = (currentPage - 1) * pageSize;
        const pageWebsites = filtered.slice(firstIndex, firstIndex + pageSize);

        tableBody.replaceChildren();
        pageWebsites.forEach(function(website) {
            const row = document.createElement('tr');
            const nameCell = document.createElement('td');
            const name = document.createElement('strong');
            const link = document.createElement('a');
            const agency = document.createElement('span');
            name.textContent = website.nama_website;
            link.href = website.url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.textContent = website.url;
            agency.className = 'monitoring-agency';
            agency.textContent = website.instansi;
            nameCell.append(name, agency, link);
            row.appendChild(nameCell);

            const statusCell = appendCell(row, '', '');
            const statusBadge = document.createElement('span');
            statusBadge.className = getStatusClass(website.status);
            statusBadge.textContent = website.status === 'Offline' ? 'DOWN' : website.status.toUpperCase();
            statusCell.appendChild(statusBadge);
            appendCell(row, formatSeconds(website.response_time));
            appendCell(row, formatMonitoringDateTime(website.last_checked_at));
            tableBody.appendChild(row);
        });

        document.getElementById('websiteEmptyState').hidden = filtered.length > 0;
        document.getElementById('websiteResultCount').textContent = filtered.length ?
            `Menampilkan ${firstIndex + 1}-${Math.min(firstIndex + pageSize, filtered.length)} dari ${filtered.length} website` :
            '0 website ditemukan';
        document.getElementById('pageStatus').textContent = `Halaman ${currentPage} dari ${pageCount}`;
        document.getElementById('previousPage').disabled = currentPage <= 1;
        document.getElementById('nextPage').disabled = currentPage >= pageCount;
    }

    function updateWebsites(updatedWebsites) {
        const updatedById = new Map(updatedWebsites.map(function(website) {
            return [String(website.id), website];
        }));
        websites = websites.map(function(website) {
            const updated = updatedById.get(String(website.id));
            return updated ? {
                ...website,
                ...updated
            } : website;
        });
        updateSummary();
        renderWebsiteTable();
    }

    async function refreshMonitoring() {
        if (isRefreshing) return;
        isRefreshing = true;
        checkButton.disabled = true;
        statusText.textContent = 'Sedang memperbarui hasil monitoring...';

        try {
            const response = await fetch(realtimeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) throw new Error('Gagal mengambil data monitoring.');
            const data = await response.json();
            updateWebsites(data.websites);
            updateCharts(data.websites, data.checked_at);
            statusText.textContent = `Diperbarui ${formatMonitoringDateTime(data.checked_at)} · otomatis setiap 30 detik`;
        } catch (error) {
            statusText.textContent = error.message;
        } finally {
            checkButton.disabled = false;
            isRefreshing = false;
        }
    }

    checkForm.addEventListener('submit', function(event) {
        event.preventDefault();
        refreshMonitoring();
    });

    Object.keys(initialHistory).forEach(function(websiteId) {
        performanceHistory[websiteId] = (initialHistory[websiteId] || []).slice(-24);
    });
    updateSummary();
    renderWebsiteTable();
    if (websites.length) drawWebsiteChart();
    searchInput.addEventListener('input', function() {
        currentPage = 1;
        renderWebsiteTable();
    });
    statusFilter.addEventListener('change', function() {
        currentPage = 1;
        renderWebsiteTable();
    });
    pageSizeInput.addEventListener('change', function() {
        currentPage = 1;
        renderWebsiteTable();
    });
    document.getElementById('previousPage').addEventListener('click', function() {
        currentPage -= 1;
        renderWebsiteTable();
    });
    document.getElementById('nextPage').addEventListener('click', function() {
        currentPage += 1;
        renderWebsiteTable();
    });
    document.querySelectorAll('[data-status-filter]').forEach(function(button) {
        button.addEventListener('click', function() {
            statusFilter.value = button.dataset.statusFilter;
            currentPage = 1;
            renderWebsiteTable();
        });
    });
    chartSelector.addEventListener('change', drawWebsiteChart);
    window.addEventListener('resize', drawWebsiteChart);
    refreshMonitoring();
    setInterval(refreshMonitoring, 30000);
</script>

@endsection