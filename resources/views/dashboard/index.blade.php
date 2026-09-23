<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Website Kota Metro</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f8f6;
            color: #17324d;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 290px;
            height: 100vh;
            padding: 25px 16px;
            background: #075c49;
            color: white;
        }

        .logo {
            padding-bottom: 22px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,.2);
        }

        .logo h2 {
            margin: 10px 0 5px;
            font-size: 24px;
            line-height: 1.2;
        }

        .logo p {
            margin: 0;
            color: #d5f5eb;
            font-size: 14px;
        }

        .menu-title {
            margin: 25px 14px 10px;
            color: #a8d8cc;
            font-size: 13px;
            text-transform: uppercase;
        }

        .menu a {
            display: block;
            margin-bottom: 5px;
            padding: 14px 16px;
            border-radius: 9px;
            color: white;
            text-decoration: none;
        }

        .menu a:hover,
        .menu a.active {
            background: #178c68;
        }

        .logout {
            position: absolute;
            bottom: 25px;
            left: 30px;
        }

        .logout button {
            padding: 0;
            border: 0;
            background: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        /* MAIN */
        .main {
            min-height: 100vh;
            margin-left: 290px;
        }

        .topbar {
            height: 80px;
            padding: 0 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            border-bottom: 1px solid #e2e8e5;
        }

        .topbar-left {
            color: #55708c;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .monitoring-active {
            color: #087c54;
        }

        .monitoring-active span {
            display: inline-block;
            width: 9px;
            height: 9px;
            margin-right: 6px;
            border-radius: 50%;
            background: #20b978;
        }

        .admin-name {
            font-weight: bold;
        }

        .content {
            padding: 30px 35px;
        }

        /* HEADER */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
        }

        .page-header h1 {
            margin: 0 0 6px;
            color: #075c49;
            font-size: 32px;
        }

        .page-header p {
            margin: 0;
            color: #55708c;
            font-size: 17px;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-check-all {
            border: 0;
            background: #168c68;
            color: white;
        }

        .btn-check-all:hover {
            background: #0d7555;
        }

        .btn-add {
            border: 1px solid #ccd7d3;
            background: white;
            color: #17324d;
        }

        /* ALERT */
        .alert {
            margin-bottom: 22px;
            padding: 14px 18px;
            border: 1px solid #b8ebc9;
            border-radius: 8px;
            background: #e6f8ec;
            color: #087c54;
        }

        /* STATISTICS */
        .statistics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            padding: 25px;
            border-radius: 13px;
            background: white;
            box-shadow: 0 3px 12px rgba(0,0,0,.06);
        }

        .stat-title {
            margin-bottom: 18px;
            color: #58718a;
            font-size: 16px;
            font-weight: bold;
        }

        .stat-number {
            font-size: 40px;
            font-weight: bold;
        }

        .total {
            color: #17324d;
        }

        .online-number {
            color: #0aa568;
        }

        .offline-number {
            color: #df2929;
        }

        /* TABLE */
        .website-container {
            padding: 25px 30px;
            border-radius: 13px;
            background: white;
            box-shadow: 0 3px 12px rgba(0,0,0,.06);
        }

        .table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .table-header h2 {
            margin: 0;
            color: #17324d;
            font-size: 24px;
        }

        .search {
            width: 300px;
            padding: 11px 15px;
            border: 1px solid #ccd7d3;
            border-radius: 8px;
            outline: none;
        }

        .search:focus {
            border-color: #168c68;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }

        th {
            padding: 14px;
            background: #e6f4e9;
            color: #086b4d;
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e9e7;
            font-size: 14px;
        }

        tr:hover {
            background: #f8fbfa;
        }

        .website-name {
            font-weight: bold;
        }

        .url {
            display: block;
            max-width: 230px;
            overflow: hidden;
            color: #2463d4;
            text-decoration: none;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .url:hover {
            text-decoration: underline;
        }

        /* STATUS */
        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .status-online {
            background: #d7f8e5;
            color: #087c54;
        }

        .status-offline {
            background: #ffe0e0;
            color: #d92929;
        }

        .status-warning {
            background: #fff0c9;
            color: #a66a00;
        }

        .status-unchecked {
            background: #e8ecea;
            color: #65736e;
        }

        /* AKSI */
        .action-form {
            margin: 0;
        }

        .btn-action {
            padding: 8px 16px;
            border: 0;
            border-radius: 7px;
            background: #168c68;
            color: white;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-action:hover {
            background: #0d7555;
        }

        /* FOOTER */
        footer {
            padding: 25px;
            color: #6c8193;
            text-align: center;
            font-size: 14px;
        }

        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .sidebar {
                width: 220px;
            }

            .main {
                margin-left: 220px;
            }

            .statistics {
                grid-template-columns: 1fr;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 0 20px;
            }

            .topbar-left {
                display: none;
            }

            .table-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 15px;
            }

            .search {
                width: 100%;
            }
        }

            .logo {
    text-align: center;
}

        .logo-metro {
        width: 75px !important;
        height: 75px !important;
        object-fit: contain;
        display: block;
        margin: 0 auto 8px auto;
        }

        .logo h2 {
        font-size: 22px;
        line-height: 1.2;
        margin: 0;
        }

        .logo p {
        font-size: 14px;
        margin-top: 8px;
        }
    </style>
</head>

<body>
<!-- SIDEBAR -->
<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="logo">

        <img src="{{ asset('images/logokomet.png') }}"
             alt="Logo Kota Metro"
             class="logo-metro">

        <h2>PEMERINTAH KOTA<br>METRO</h2>
        <p>Monitoring Website Kota Metro</p>

    </div>
        <div class="menu">
    <a href="{{ route('dashboard') }}" class="active">
        Dashboard
    </a>

    <a href="{{ route('website.index') }}">
        Daftar Website
    </a>

    <a href="{{ route('monitoring.index') }}">
        Monitoring
    </a>

    <a href="{{ route('riwayat.index') }}">
        Riwayat Monitoring
    </a>
</div>
<div class="menu-title">Sistem</div>
<div class="menu">
    <a href="{{ route('pengaturan.index') }}">
        Pengaturan
    </a>
</div>
        <div class="logout">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main">

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
        <section class="content">

            <!-- HEADER -->
            <div class="page-header">
                <div>
                    <h1>Dashboard Monitoring</h1>
                    <p>Pantau kondisi website dan aplikasi Pemerintah Kota Metro.</p>
                </div>

                <div class="header-buttons">

                    <form action="{{ route('website.check.all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-check-all">
                            ✓ Cek Semua Website
                        </button>
                    </form>

                    <a href="{{ route('website.create') }}" class="btn btn-add">
                        ＋ Tambah Website
                    </a>

                </div>
            </div>

            <!-- ALERT -->
            @if(session('success'))
                <div class="alert">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <!-- STATISTICS -->
            @php
                $totalWebsite = $websites->count();
                $websiteOnline = $websites->where('status', 'Online')->count();
                $websiteOffline = $websites->where('status', 'Offline')->count();
            @endphp

            <div class="statistics">

                <div class="stat-card">
                    <div class="stat-title">TOTAL WEBSITE</div>
                    <div class="stat-number total">
                        {{ $totalWebsite }}
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">🟢 WEBSITE AKTIF</div>
                    <div class="stat-number online-number">
                        {{ $websiteOnline }}
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-title">🔴 WEBSITE TIDAK AKTIF</div>
                    <div class="stat-number offline-number">
                        {{ $websiteOffline }}
                    </div>
                </div>

            </div>

            <!-- WEBSITE -->
            <div class="website-container">

                <div class="table-header">
                    <h2>Daftar Website Kota Metro</h2>

                    <input
                        type="text"
                        id="searchWebsite"
                        class="search"
                        placeholder="Cari website atau instansi..."
                        onkeyup="searchTable()"
                    >
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
                                <th>RESPONSE</th>
                                <th>TERAKHIR DICEK</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($websites as $index => $website)

                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        <span class="website-name">
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
                                            title="{{ $website->url }}"
                                        >
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
                                        @if($website->response_time)
                                            {{ $website->response_time }} ms
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
                                            class="action-form"
                                        >
                                            @csrf

                                            <button type="submit" class="btn-action">
                                                Cek
                                            </button>
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
            </div>

        </section>

        <footer>
            © {{ date('Y') }} Pemerintah Kota Metro |
            Sistem Monitoring Website
        </footer>

    </main>

    <!-- SEARCH -->
    <script>
        function searchTable() {
            const input = document.getElementById('searchWebsite');
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll('#websiteTable tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        }
    </script>

</body>
</html>