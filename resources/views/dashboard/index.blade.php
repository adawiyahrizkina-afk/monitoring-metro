<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Monitoring Website Kota Metro</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f8f6;
            color: #263238;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 245px;
            height: 100vh;

            background: #064e3b;
            color: white;

            padding: 25px 15px;
        }

        .logo {
            text-align: center;

            border-bottom: 1px solid
                rgba(255,255,255,.15);

            padding-bottom: 25px;

            margin-bottom: 25px;
        }

        .logo-icon {
            font-size: 40px;
        }

        .logo h2 {
            margin: 8px 0 3px;

            font-size: 19px;
        }

        .logo p {
            margin: 0;

            font-size: 12px;

            color: #b7d8cc;
        }

        .menu-title {
            font-size: 11px;

            color: #9fc7ba;

            margin: 20px 12px 8px;

            text-transform: uppercase;
        }

        .menu {
            display: block;

            text-decoration: none;

            color: #dceee8;

            padding: 13px 14px;

            border-radius: 8px;

            margin-bottom: 5px;

            font-size: 14px;
        }

        .menu:hover,
        .menu.active {
            background: #16865f;

            color: white;
        }

        .sidebar-bottom {
            position: absolute;

            bottom: 20px;

            left: 15px;

            right: 15px;
        }


        /* ================= MAIN ================= */

        .main {
            margin-left: 245px;

            min-height: 100vh;
        }


        /* ================= TOPBAR ================= */

        .topbar {
            height: 70px;

            background: white;

            border-bottom: 1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 30px;
        }

        .topbar-left {
            font-size: 14px;

            color: #64748b;
        }

        .topbar-right {
            display: flex;

            align-items: center;

            gap: 15px;
        }

        .monitoring-active {
            display: flex;

            align-items: center;

            gap: 7px;

            font-size: 13px;

            color: #15803d;
        }

        .green-dot {
            width: 9px;
            height: 9px;

            border-radius: 50%;

            background: #22c55e;
        }

        .admin {
            font-weight: bold;

            color: #334155;
        }


        /* ================= CONTENT ================= */

        .content {
            padding: 30px;
        }

        .heading {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;
        }

        .heading h1 {
            margin: 0;

            color: #064e3b;

            font-size: 28px;
        }

        .heading p {
            margin: 7px 0 0;

            color: #64748b;

            font-size: 14px;
        }

        .heading-actions {
            display: flex;

            gap: 10px;
        }

        button,
        .btn {
            border: none;

            cursor: pointer;

            border-radius: 7px;

            padding: 11px 17px;

            font-size: 14px;

            text-decoration: none;
        }

        .btn-green {
            background: #16865f;

            color: white;
        }

        .btn-green:hover {
            background: #0f704f;
        }

        .btn-white {
            background: white;

            border: 1px solid #d1d5db;

            color: #334155;
        }


        /* ================= ALERT ================= */

        .alert {
            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            background: #e8f5e9;

            color: #166534;

            border: 1px solid #bbf7d0;
        }


        /* ================= STAT CARDS ================= */

        .stats {
            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-bottom: 25px;
        }

        .stat-card {
            background: white;

            border-radius: 12px;

            padding: 22px;

            box-shadow:
                0 3px 12px rgba(0,0,0,.06);

            border: 1px solid #edf2ef;
        }

        .stat-top {
            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .stat-title {
            color: #64748b;

            font-size: 14px;

            font-weight: bold;
        }

        .stat-icon {
            width: 40px;
            height: 40px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 10px;

            font-size: 20px;
        }

        .icon-total {
            background: #dbeafe;
        }

        .icon-online {
            background: #dcfce7;
        }

        .icon-offline {
            background: #fee2e2;
        }

        .stat-number {
            font-size: 34px;

            font-weight: bold;

            margin-top: 15px;
        }

        .number-online {
            color: #16a34a;
        }

        .number-offline {
            color: #dc2626;
        }


        /* ================= WEBSITE TABLE ================= */

        .website-box {
            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 3px 12px rgba(0,0,0,.06);

            border: 1px solid #edf2ef;
        }

        .table-header {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;
        }

        .table-header h2 {
            margin: 0;

            color: #1e293b;

            font-size: 20px;
        }

        .search {
            width: 300px;

            padding: 11px 14px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            outline: none;
        }

        .search:focus {
            border-color: #16865f;
        }

        table {
            width: 100%;

            border-collapse: collapse;
        }

        th {
            background: #e8f5e9;

            color: #166534;

            padding: 13px 12px;

            font-size: 13px;

            text-align: left;
        }

        td {
            padding: 14px 12px;

            border-bottom: 1px solid #edf0ee;

            font-size: 13px;
        }

        tr:hover {
            background: #f8faf9;
        }

        .website-name {
            font-weight: bold;

            color: #1e293b;
        }

        .url {
            color: #2563eb;

            text-decoration: none;

            font-size: 12px;
        }

        /* STATUS */

        .status {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;

            font-weight: bold;
        }

        .status-online {
            background: #dcfce7;

            color: #15803d;
        }

        .status-offline {
            background: #fee2e2;

            color: #dc2626;
        }

        .status-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;
        }

        .dot-online {
            background: #22c55e;
        }

        .dot-offline {
            background: #ef4444;
        }

        .check-btn {
            background: #16865f;

            color: white;

            padding: 7px 12px;

            border-radius: 5px;

            border: none;

            cursor: pointer;

            font-size: 12px;
        }

        .check-btn:hover {
            background: #0f704f;
        }

        .empty {
            text-align: center;

            padding: 40px;

            color: #64748b;
        }


        /* ================= FOOTER ================= */

        footer {
            padding: 25px 30px;

            color: #64748b;

            font-size: 12px;

            text-align: center;
        }


        /* ================= RESPONSIVE ================= */

        @media(max-width: 1000px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .heading {
                display: block;
            }

            .heading-actions {
                margin-top: 15px;
            }

        }

        @media(max-width: 700px) {

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .table-header {
                display: block;
            }

            .search {
                width: 100%;

                margin-top: 15px;
            }

            .website-box {
                overflow-x: auto;
            }

        }

    </style>

</head>


<body>


<!-- ================= SIDEBAR ================= -->

<aside class="sidebar">

    <div class="logo">

        <div class="logo-icon">
        </div>

        <h2>
            PEMERINTAH KOTA
            <br>
            METRO
        </h2>

        <p>
            Monitoring Website Kota Metro
        </p>

    </div>


    <div class="menu-title">
        MENU UTAMA
    </div>


    <a
        href="{{ route('dashboard') }}"
        class="menu active"
    >
        &nbsp; Dashboard
    </a>


    <a
        href="#daftar-website"
        class="menu"
    >
         &nbsp; Daftar Website
    </a>


    <a
        href="#daftar-website"
        class="menu"
    >
        &nbsp; Monitoring
    </a>


    <a
        href="#"
        class="menu"
    >
        &nbsp; Riwayat Monitoring
    </a>


    <div class="menu-title">
        SISTEM
    </div>


    <a
        href="#"
        class="menu"
    >
        &nbsp; Pengaturan
    </a>


    <div class="sidebar-bottom">

        <form
            action="{{ route('logout') }}"
            method="POST"
        >

            @csrf

            <button
                type="submit"
                class="menu"
                style="
                    width:100%;
                    text-align:left;
                    background:transparent;
                    color:white;
                "
            >

             &nbsp; Logout

            </button>

        </form>

    </div>

</aside>


<!-- ================= MAIN ================= -->

<main class="main">


    <!-- TOPBAR -->

    <div class="topbar">

        <div class="topbar-left">

            Sistem Monitoring Website

        </div>


        <div class="topbar-right">

            <div class="monitoring-active">

                <span class="green-dot"></span>

                Monitoring Aktif

            </div>

            <div class="admin">

                {{ Auth::user()->name }}

            </div>

        </div>

    </div>


    <!-- CONTENT -->

    <div class="content">


        <!-- HEADING -->

        <div class="heading">

            <div>

                <h1>
                    Dashboard Monitoring
                </h1>

                <p>
                    Pantau kondisi website dan aplikasi
                    Pemerintah Kota Metro.
                </p>

            </div>


            <div class="heading-actions">

                <form
                    action="{{ route('website.check.all') }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-green"
                    >
                        Cek Semua Website
                    </button>

                </form>

                <a
                    href="#"
                    class="btn btn-white"
                >
                    Tambah Website
                </a>

            </div>

        </div>


        <!-- ALERT -->
        @if(session('success'))
            <div class="alert">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- STATISTIK -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-title">
                        TOTAL WEBSITE
                    </div>
                    
                    <div class="stat-icon icon-total">
                    </div>

                </div>
                <div class="stat-number">
                    {{ $total }}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-title">
                        WEBSITE AKTIF
                    </div>

                    <div class="stat-icon icon-online">
                        ✓
                    </div>
                </div>
                <div class="stat-number number-online">
                    {{ $online }}
                </div>
            </div>
            <div class="stat-card">

                <div class="stat-top">

                    <div class="stat-title">
                        WEBSITE TIDAK AKTIF
                    </div>

                    <div class="stat-icon icon-offline">
                        ✕
                    </div>
                </div>
                <div class="stat-number number-offline">
                    {{ $offline }}
                </div>
            </div>
        </div>
        <!-- WEBSITE -->
        <div
            class="website-box"
            id="daftar-website"
        >
            <div class="table-header">
                <div>

                    <h2>
                        Daftar Website Kota Metro
                    </h2>

                </div>

                <input
                    type="text"
                    id="searchWebsite"
                    class="search"
                    placeholder="Cari website atau instansi..."
                >
            </div>
            <table>
                <thead>
                    <tr>

                        <th>
                            NO
                        </th>

                        <th>
                            NAMA WEBSITE
                        </th>

                        <th>
                            INSTANSI
                        </th>

                        <th>
                            URL
                        </th>

                        <th>
                            STATUS
                        </th>

                        <th>
                            RESPONSE
                        </th>

                        <th>
                            TERAKHIR DICEK
                        </th>

                        <th>
                            AKSI
                        </th>
                    </tr>
                </thead>
                <tbody id="websiteTable">
                    @forelse($websites as $website)
                    <tr>
                        <td>
                            {{ $loop->iteration }}
                        </td>
                        <td>

                            <div class="website-name">

                                {{ $website->nama_website }}

                            </div>
                        </td>
                        <td>
                            {{ $website->instansi }}
                        </td>
                        <td>
                            <a
                                href="{{ $website->url }}"
                                target="_blank"
                                class="url"
                            >

                                {{ $website->url }}
                            </a>
                        </td>
                        <td>
                            @if($website->status == 'Online')

                                <span
                                    class="status status-online"
                                >

                                    <span
                                        class="status-dot dot-online"
                                    ></span>

                                    ONLINE

                                </span>

                            @else

                                <span
                                    class="status status-offline"
                                >

                                    <span
                                        class="status-dot dot-offline"
                                    ></span>

                                    OFFLINE

                                </span>
                            @endif
                        </td>
                        <td>
                            @if($website->response_time)

                                {{ $website->response_time }}
                                ms

                            @else

                                -
                            @endif
                        </td>
                        <td>
                            @if($website->last_checked_at)

                                {{ $website->last_checked_at
                                    ->format('d/m/Y H:i') }}

                            @else
                                Belum dicek
                            @endif
                        </td>
                        <td>
                            <form
                                action="{{ route(
                                    'website.check',
                                    $website->id
                                ) }}"
                                method="POST"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="check-btn"
                                >
                                    Cek
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="8"
                            class="empty"
                        >
                            <br><br>
                            Belum ada website
                            yang terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <footer>

        © {{ date('Y') }}
        Pemerintah Kota Metro
        |
        Sistem Monitoring Website

    </footer>
</main>
<script>
    const search =
        document.getElementById('searchWebsite');

    search.addEventListener('keyup', function () {

        const keyword =
            this.value.toLowerCase();

        const rows =
            document.querySelectorAll(
                '#websiteTable tr'
            );

        rows.forEach(function (row) {
            const text =
                row.innerText.toLowerCase();
            if (text.includes(keyword)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

</script>
</body>
</html>