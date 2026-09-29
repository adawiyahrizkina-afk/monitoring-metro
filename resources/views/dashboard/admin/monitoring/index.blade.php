@extends('layouts.admin')

@section('title', 'Monitoring Website')

@section('content')

<div class="page-header">

    <h1>Monitoring Website</h1>

    <p>
        Melakukan pengecekan status website Pemerintah Kota Metro.
    </p>

</div>


<div class="card">

    <h2>Monitoring Website Kota Metro</h2>

    <p>
        Gunakan fitur pengecekan untuk mengetahui apakah
        website dapat diakses atau tidak.
    </p>

    <div style="display: flex; gap: 10px;">
        <form action="{{ route('monitoring.realtime') }}" method="POST" id="realtimeCheckForm">

            @csrf

            <button type="submit" class="btn" id="realtimeCheckButton">
                ✓ Cek Semua Website
            </button>

        </form>

        <input
            type="text"
            id="searchWebsite"
            class="search"
            placeholder="Cari website atau instansi..."
            onkeyup="searchTable()">
    </div>

    <p class="realtime-status" id="realtimeStatus">Memuat data monitoring...</p>

</div>


<div class="card">

    <h2>Hasil Monitoring</h2>

    <div class="website-charts">
        @forelse($websites ?? [] as $website)
        <div class="monitoring-chart" data-chart-website-id="{{ $website->id }}">
            <div class="chart-heading">
                <h3>{{ $website->nama_website }}</h3>
                <span>{{ $website->instansi }}</span>
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px;">
                    <span>
                        <a
                            href="{{ $website->url }}"
                            target="_blank"
                            class="url"
                            title="{{ $website->url }}">
                            {{ $website->url }}
                        </a>
                    </span>
                    <span>
                        <tbody>
                            <td data-field="status">

                                @if(($website->status ?? '') == 'Online')

                                <span class="status-online">
                                    ● ONLINE
                                </span>

                                @elseif(($website->status ?? '') == 'Warning')

                                <span class="status status-warning">
                                    ● WARNING
                                </span>

                                @elseif(($website->status ?? '') == 'Offline')

                                <span class="status-offline">
                                    ● OFFLINE
                                </span>

                                @else

                                <span class="status-unchecked">
                                    ● BELUM DICEK
                                </span>

                                @endif

                            </td>
                        </tbody>
                    </span>
                </div>
                <span>Skor tinggi = cepat · WARNING = lambat · DOWN = tidak dapat diakses</span>
            </div>
            <div class="chart-meta">
                <strong data-field="score">-</strong>
                <span>Skor performa</span>
            </div>

            <!-- Bagian grafik/chart -->
            <canvas class="website-performance-chart" height="210"></canvas>
            <div class="chart-events" data-field="events" aria-live="polite"></div>
        </div>
        @empty
        <p>Belum ada website untuk dimonitor.</p>
        @endforelse
    </div>

</div>

<script>
    const realtimeUrl = @json(route('monitoring.realtime'));
    const csrfToken = @json(csrf_token());
    const initialWebsites = @json($websites ?? []);
    const initialHistory = @json($chartHistory ?? []);
    const statusText = document.getElementById('realtimeStatus');
    const checkForm = document.getElementById('realtimeCheckForm');
    const checkButton = document.getElementById('realtimeCheckButton');
    const performanceHistory = {};
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
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date(value));
    }

    function drawWebsiteChart(websiteId) {
        const canvas = document.querySelector(`[data-chart-website-id="${websiteId}"] canvas`);
        if (!canvas) return;
        const chartContext = canvas.getContext('2d');
        const width = canvas.clientWidth || 500;
        const height = 210;
        const deviceRatio = window.devicePixelRatio || 1;
        canvas.width = width * deviceRatio;
        canvas.height = height * deviceRatio;
        chartContext.setTransform(deviceRatio, 0, 0, deviceRatio, 0, 0);
        chartContext.clearRect(0, 0, width, height);

        chartContext.strokeStyle = '#d9e5df';
        chartContext.fillStyle = '#55708c';
        chartContext.font = '12px Arial';

        [0, 25, 50, 75, 100].forEach(function(value) {
            const y = height - 38 - (value / 100) * (height - 58);
            chartContext.beginPath();
            chartContext.moveTo(42, y);
            chartContext.lineTo(width - 15, y);
            chartContext.stroke();
            chartContext.fillText(value, 12, y + 4);
        });

        const history = performanceHistory[websiteId] || [];
        if (!history.length) {
            chartContext.fillText('Belum ada data monitoring.', 55, height / 2);
            return;
        }

        const plotWidth = Math.max(width - 70, 1);
        const timestamps = history.map(function(entry) {
            return new Date(entry.checked_at).getTime();
        });
        const firstTimestamp = timestamps[0];
        const timeRange = timestamps[timestamps.length - 1] - firstTimestamp;
        chartContext.strokeStyle = '#168c68';
        chartContext.lineWidth = 3;
        chartContext.beginPath();

        const points = history.map(function(entry, index) {
            const x = 45 + (timeRange > 0 ? ((timestamps[index] - firstTimestamp) / timeRange) * plotWidth :
                (history.length === 1 ? 0 : (index / (history.length - 1)) * plotWidth));
            const y = height - 38 - (entry.score / 100) * (height - 58);
            return {
                x,
                y,
                score: entry.score,
                status: entry.status,
                checkedAt: entry.checked_at
            };
        });

        chartContext.beginPath();
        points.forEach(function(point, index) {
            index === 0 ?
                chartContext.moveTo(point.x, point.y) :
                chartContext.lineTo(point.x, point.y);
        });
        chartContext.stroke();

        points.forEach(function(point, index) {
            const x = point.x;
            const y = point.y;
            chartContext.fillStyle = point.status === 'Offline' ? '#c83b3b' :
                point.status === 'Warning' ? '#d47b16' : '#168c68';
            chartContext.beginPath();
            chartContext.arc(x, y, 5, 0, Math.PI * 2);
            chartContext.fill();

            if (index % Math.max(1, Math.ceil(points.length / 6)) === 0 || index === points.length - 1) {
                chartContext.fillStyle = '#55708c';
                chartContext.textAlign = 'center';
                chartContext.fillText(formatMonitoringTime(point.checkedAt), x, height - 8);
            }
        });
        chartContext.textAlign = 'start';
    }

    function updateCharts(websites, checkedAt) {
        websites.forEach(function(website) {
            if (!performanceHistory[website.id]) performanceHistory[website.id] = [];
            performanceHistory[website.id].push({
                score: calculateScore(website),
                status: website.status,
                checked_at: website.last_checked_at || checkedAt
            });
            performanceHistory[website.id] = performanceHistory[website.id].slice(-12);
            const chartCard = document.querySelector(`[data-chart-website-id="${website.id}"]`);
            if (chartCard) {
                chartCard.querySelector('[data-field="score"]').textContent = `${calculateScore(website)} / 100`;
                updateChartEvents(chartCard, performanceHistory[website.id]);
            }
            drawWebsiteChart(website.id);
        });
    }

    function updateChartEvents(chartCard, history) {
        const eventList = chartCard.querySelector('[data-field="events"]');
        const events = history.filter(function(entry) {
            return entry.status === 'Warning' || entry.status === 'Offline';
        }).slice().reverse();

        eventList.replaceChildren();
        if (!events.length) {
            eventList.textContent = 'Tidak ada gangguan pada pengecekan terakhir.';
            return;
        }

        events.forEach(function(entry) {
            const event = document.createElement('span');
            event.className = entry.status === 'Offline' ? 'chart-event is-offline' : 'chart-event is-warning';
            event.textContent = `${entry.status === 'Offline' ? 'DOWN' : 'LAMBAT'} · ${formatMonitoringDateTime(entry.checked_at)}`;
            eventList.appendChild(event);
        });
    }

    function updateTable(websites) {
        const statusClasses = {
            Online: 'status-online',
            Offline: 'status-offline',
            Warning: 'status-warning',
            'Belum Dicek': 'status-unchecked'
        };

        websites.forEach(function(website) {
            const row = document.querySelector(`[data-website-id="${website.id}"]`);
            if (!row) return;
            const statusClass = statusClasses[website.status] || 'status-unchecked';
            row.querySelector('[data-field="status"]').innerHTML = `<span class="${statusClass}">● ${website.status.toUpperCase()}</span>`;
            row.querySelector('[data-field="response"]').textContent = formatSeconds(website.response_time);
        });
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
            updateTable(data.websites);
            updateCharts(data.websites, data.checked_at);
            statusText.textContent = `Diperbarui ${data.checked_at}. Pembaruan otomatis setiap 30 detik.`;
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

    initialWebsites.forEach(function(website) {
        performanceHistory[website.id] = (initialHistory[website.id] || []).slice(-24);
        const chartCard = document.querySelector(`[data-chart-website-id="${website.id}"]`);
        if (chartCard) {
            chartCard.querySelector('[data-field="score"]').textContent = `${calculateScore(website)} / 100`;
            updateChartEvents(chartCard, performanceHistory[website.id]);
        }
        drawWebsiteChart(website.id);
    });
    refreshMonitoring();
    setInterval(refreshMonitoring, 30000);
</script>

<script src="/js/search.js"></script>

@endsection