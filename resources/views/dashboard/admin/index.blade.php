@extends('layouts.admin')

@section('title', 'Dashboard Monitoring Website Kota Metro')

@section('content')

<!-- BAGIAN DASHBOARD AWAL -->

<section id="dashboard_utama">
    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">Sistem Monitoring Website</div>

        <div class="topbar-right">
            <div class="monitoring-active">
                <span></span>Monitoring Aktif
            </div>

            <div class="admin-name">
                {{ Auth::user()->name }}
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <section class="content" style="position: relative;">

        <!-- HEADER -->
        <div class="page-header">
            <div>
                <h1>Dashboard Monitoring</h1>
                <p>Pantau kondisi website dan aplikasi Pemerintah Kota Metro.</p>
            </div>

            <div class="header-buttons">

                <form
                    action="{{ route('website.check.all') }}"
                    method="POST"
                    id="checkAllForm">
                    @csrf
                    <button type="submit" class="btn btn-check-all" id="checkAllButton">
                        ✓ Cek Semua Website
                    </button>
                </form>

                <button class="btn btn-add" onclick="tambah()">
                    ＋ Tambah Website
                </button>

            </div>
        </div>

        <!-- ALERT -->
        @if(session('success'))
        <div class="alert">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
        @endif

        <!-- STATISTICS -->
        <div class="statistics">

            <div class="stat-card">
                <div class="stat-title">TOTAL WEBSITE</div>
                <div class="stat-number total">
                    {{ $total }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">🟢 WEBSITE AKTIF</div>
                <div class="stat-number online-number">
                    {{ $online }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">🔴 WEBSITE TIDAK AKTIF</div>
                <div class="stat-number offline-number">
                    {{ $offline }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">WARNING</div>
                <div class="stat-number warning-number">
                    {{ $warning }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">BELUM DICEK</div>
                <div class="stat-number unchecked-number">
                    {{ $unchecked }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-title">MONITORING AKTIF</div>
                <div class="stat-number active-number">
                    {{ $monitoringActive }}
                </div>
            </div>

        </div>

        <!-- WEBSITE -->
        <div class="website-container">

            <div class="table-header">
                <h2>Daftar Website Kota Metro</h2>

                <div style="gap: 5px; display: flex; align-items: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="gray">
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z" />
                    </svg>
                    <input
                        type="text"
                        id="searchWebsite"
                        class="search"
                        placeholder="Cari website atau instansi..."
                        onkeyup="searchTable()">
                </div>
            </div>

            <div class="table-wrapper">

                <table id="websiteTable">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>NAMA WEBSITE</th>
                            <th>INSTANSI</th>
                            <th>URL</th>
                            <th>STATUS</th>
                            <th>RESPONS (DETIK)</th>
                            <th>TERAKHIR DICEK</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($websites as $index => $website)

                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <span class="website-name website-name-with-favicon">
                                    <img
                                        src="{{ $website->favicon_url }}"
                                        alt=""
                                        class="website-favicon"
                                        onerror="this.hidden = true">
                                    {{ $website->nama_website }}
                                </span>
                            </td>

                            <td>
                                {{ $website->instansi }}
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

                            <td>
                                @if($website->status === 'Online')
                                <span class="status status-online">
                                    ● ONLINE
                                </span>
                                @elseif($website->status === 'Offline')
                                <span class="status status-offline">
                                    ● OFFLINE
                                </span>
                                @elseif($website->status === 'Warning')
                                <span class="status status-warning">
                                    ● WARNING
                                </span>
                                @else
                                <span class="status status-unchecked">
                                    ● BELUM DICEK
                                </span>
                                @endif
                            </td>

                            <td>
                                @if($website->response_time !== null)
                                {{ number_format($website->response_time / 1000, 2, ',', '.') }} detik
                                @else
                                -
                                @endif
                            </td>

                            <td>
                                @if($website->last_checked_at)
                                {{ $website->last_checked_at->format('d/m/Y H:i') }}
                                @else
                                Belum pernah
                                @endif
                            </td>

                            <td>
                                <form
                                    action="{{ route('website.check', $website->id) }}"
                                    method="POST"
                                    class="action-form">
                                    @csrf
                                    <button type="submit" class="btn-action">Cek</button>
                                </form>
                            </td>
                        </tr>

                        @empty

                        <tr>
                            <td colspan="8" style="text-align:center;">
                                Belum ada website yang terdaftar.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>
            <div style="position:fixed; top: 20%; left: 20px; right: 20px; max-width: inherit; margin: 0; text-align: center;">
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

                            <button type="button" class="delete" style="width: 100%;" onclick="batal()" data-close-popup>
                                Batal
                            </button>


                            <button type="submit" class="btn" style="width: 100%;">
                                Simpan Website
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>

    <footer>
        © {{ date('Y') }} Pemerintah Kota Metro |
        Sistem Monitoring Website
    </footer>
</section>

<section id="daftar_website">

</section>
<!-- SEARCH -->
<script src="/js/search.js"></script>
<script>
    let popupCloseTimer;

    function bukaPopup(popup) {
        window.clearTimeout(popupCloseTimer);
        popup.classList.remove('is-closing');
        popup.style.display = 'block';
        void popup.offsetWidth;
        popup.classList.add('is-open');
        popup.setAttribute('aria-hidden', 'false');
    }

    function tutupPopup(popup) {
        if (!popup.classList.contains('is-open')) return;

        popup.classList.remove('is-open');
        popup.classList.add('is-closing');
        popup.setAttribute('aria-hidden', 'true');

        const selesaiMenutup = () => {
            if (!popup.classList.contains('is-closing')) return;

            popup.classList.remove('is-closing');
            popup.style.display = 'none';
        };

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            selesaiMenutup();
            return;
        }

        popup.addEventListener('animationend', selesaiMenutup, { once: true });
        popupCloseTimer = window.setTimeout(selesaiMenutup, 250);
    }

    function tambah() {
        bukaPopup(document.getElementById('tambah'));
    }

    function batal() {
        tutupPopup(document.getElementById('tambah'));
    }
</script>
@endsection