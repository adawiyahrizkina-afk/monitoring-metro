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


<!-- Bagian script -->
<script src="path/to/chartjs/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('websitePerformanceChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: [{
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'], 
                label: 'nama websitenya',
                data: [12, 19, 3, 5, 2, 3],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection