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


<div class="card" style="position: relative;">

    <button type="button" class="btn" data-open-add-website>
        + Tambah Website
    </button>

    <input
        type="text"
        id="searchWebsite"
        class="search"
        placeholder="Cari website atau instansi..."
        onkeyup="searchTable()" style="background-color: white;">

    <table id="websiteTable">

        <thead>

            <tr>
                <th>No</th>
                <th>Nama Website</th>
                <th>URL</th>
                <th>Grafik</th>
                <th>Status</th>
                <th style="text-align: center;">Interaksi</th>
            </tr>

        </thead>


        <tbody>

            @forelse($websites ?? [] as $website)

            <tr
                data-website-row
                data-website-id="{{ $website->id }}"
                data-website-name="{{ $website->nama_website }}"
                data-website-instansi="{{ $website->instansi }}"
                data-website-url="{{ $website->url }}"
                data-check-url="{{ route('website.check', $website) }}"
                data-last-checked-at="{{ $website->last_checked_at?->toIso8601String() }}">

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    <span class="website-name-with-favicon">
                        <img
                            src="{{ $website->favicon_url }}"
                            alt=""
                            class="website-favicon"
                            onerror="this.hidden = true">
                        {{ $website->nama_website ?? '-' }}
                    </span>
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
                    @if (($chartHistory[$website->id] ?? collect())->isEmpty())
                    <small data-chart-empty>Belum ada data pemeriksaan.</small>
                    @endif
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
                    <button type="button" class="edit" data-edit-website>Edit</button>
                    <form action="{{ route('website.destroy', $website) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus website ini? Riwayat monitoringnya juga akan terhapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete">Hapus</button>
                    </form>
                    <div class="website-check-control">
                        <button type="button" class="btn-action btn" data-check-website>Cek</button>
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

    <div class="pop-up" id="tambah" aria-labelledby="tambahWebsiteTitle" aria-hidden="true">

        <h1 id="tambahWebsiteTitle">Tambah Website</h1>

        <p class="description">
            Tambahkan website Pemerintah Kota Metro
            yang ingin dipantau.
        </p>

        @if ($errors->any())

        <div class="error">

            @foreach ($errors->all() as $error)

            <div>{{ $error }}</div>

            @endforeach

        </div>
        @endif
        <form
            action="{{ route('website.store') }}"
            method="POST">
            @csrf
            <label>
                Nama Website
            </label>

            <input
                type="text"
                name="nama_website"
                placeholder="Contoh: Portal Kota Metro"
                value="{{ old('nama_website') }}"
                required>


            <label for="add_instansi">
                Instansi
            </label>

            <select name="instansi" id="add_instansi" required>

                <option value="" disabled {{ old('instansi') ? '' : 'selected' }}>
                    -- Pilih Instansi --
                </option>

                @foreach (config('opd') as $opd)

                <option
                    value="{{ $opd }}"
                    {{ old('instansi') === $opd ? 'selected' : '' }}>
                    {{ $opd }}
                </option>

                @endforeach

            </select>

            <label>
                URL Website
            </label>

            <input
                type="url"
                name="url"
                placeholder="https://contoh.go.id"
                value="{{ old('url') }}"
                required>

            <div class="buttons" align="center">

                <button type="button" class="delete" style="width: 100%;" data-close-popup>
                    Batal
                </button>


                <button type="submit" class="btn" style="width: 100%;">
                    Simpan Website
                </button>

            </div>
        </form>
    </div>

    <div class="pop-up" id="edit" aria-labelledby="editWebsiteTitle" aria-hidden="true">
        <h1 id="editWebsiteTitle">Edit Website</h1>
        <p class="description">Perbarui informasi website yang dipantau.</p>

        @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form
            action="{{ route('website.update', ['website' => '__WEBSITE_ID__']) }}"
            data-update-url-template="{{ route('website.update', ['website' => '__WEBSITE_ID__']) }}"
            method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="website_id" value="{{ old('website_id') }}">

            <label for="nama_website">Nama Website</label>
            <input
                id="nama_website"
                type="text"
                name="nama_website"
                value="{{ old('nama_website') }}"
                maxlength="100"
                required>

            @php
            $daftarOpd = config('opd');
            $instansiTerpilih = old('instansi');
            @endphp

            <label for="edit_instansi">Instansi</label>
            <select name="instansi" id="edit_instansi" required>

                @foreach ($daftarOpd as $opd)
                <option
                    value="{{ $opd }}"
                    {{ $instansiTerpilih === $opd ? 'selected' : '' }}>
                    {{ $opd }}
                </option>
                @endforeach

            </select>

            <label for="url">URL Website</label>
            <input
                id="url"
                type="url"
                name="url"
                value="{{ old('url') }}"
                maxlength="255"
                required>

            <div class="buttons">
                <button type="button" class="delete" style="width: 100%;" data-close-popup>Batal</button>
                <button type="submit" class="btn" style="width: 100%;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>


@vite('resources/js/chart.js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addPopup = document.getElementById('tambah');
        const editPopup = document.getElementById('edit');
        const editForm = editPopup.querySelector('form');
        const editWebsiteId = editForm.querySelector('[name="website_id"]');
        const editName = editForm.querySelector('[name="nama_website"]');
        const editAgency = editForm.querySelector('[name="instansi"]');
        const editUrl = editForm.querySelector('[name="url"]');

        function closePopups() {
            [addPopup, editPopup].forEach((popup) => {
                popup.classList.remove('is-open');
                popup.setAttribute('aria-hidden', 'true');
            });
        }

        function openPopup(popup) {
            closePopups();
            void popup.offsetWidth;
            popup.classList.add('is-open');
            popup.setAttribute('aria-hidden', 'false');
        }

        function prepareEdit(row) {
            const websiteId = row.dataset.websiteId;
            const agency = row.dataset.websiteInstansi || '';
            const agencyOption = Array.from(editAgency.options)
                .find((option) => option.value === agency);

            if (!agencyOption && agency) {
                editAgency.add(new Option(`${agency} (data lama)`, agency));
            }

            editWebsiteId.value = websiteId;
            editName.value = row.dataset.websiteName || '';
            editAgency.value = agency;
            editUrl.value = row.dataset.websiteUrl || '';
            editForm.action = editForm.dataset.updateUrlTemplate.replace('__WEBSITE_ID__', encodeURIComponent(websiteId));
            openPopup(editPopup);
        }

        document.querySelector('[data-open-add-website]')?.addEventListener('click', () => openPopup(addPopup));
        document.querySelectorAll('[data-close-popup]').forEach((button) => {
            button.addEventListener('click', closePopups);
        });
        document.querySelectorAll('[data-edit-website]').forEach((button) => {
            button.addEventListener('click', () => prepareEdit(button.closest('[data-website-row]')));
        });

        const oldWebsiteId = @json(old('website_id'));
        const hasValidationErrors = @json($errors->any());
        if (hasValidationErrors) {
            if (oldWebsiteId) {
                const row = Array.from(document.querySelectorAll('[data-website-row]'))
                    .find((websiteRow) => websiteRow.dataset.websiteId === String(oldWebsiteId));

                if (row) {
                    prepareEdit(row);
                    editName.value = @json(old('nama_website'));
                    const oldAgency = @json(old('instansi'));
                    if (oldAgency && !Array.from(editAgency.options).some((option) => option.value === oldAgency)) {
                        editAgency.add(new Option(`${oldAgency} (data lama)`, oldAgency));
                    }
                    editAgency.value = oldAgency;
                    editUrl.value = @json(old('url'));
                }
            } else {
                openPopup(addPopup);
            }
        }

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
                const result = await readCheckResponse(response);
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
                row.querySelector('[data-chart-empty]')?.remove();
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

        async function readCheckResponse(response) {
            let data;

            try {
                data = await response.json();
            } catch {
                if (response.redirected) {
                    throw new Error('Sesi login berakhir. Silakan masuk kembali.');
                }

                throw new Error(`Server mengirim respons yang tidak valid (HTTP ${response.status}).`);
            }

            if (!response.ok) {
                throw new Error(data.message || `Pemeriksaan website gagal (HTTP ${response.status}).`);
            }

            return data;
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
    });
</script>

<script src="/js/search.js"></script>

@endsection