<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Monitoring Website Kota Metro')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f8f4;
            color: #222;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: #075f4f;
            color: white;
            padding: 25px 20px;
            overflow-y: auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            width: 65px;
            height: 65px;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .logo h2 {
            margin: 0;
            font-size: 21px;
            line-height: 1.2;
        }

        .logo p {
            font-size: 14px;
            margin-top: 25px;
        }

        .menu-title {
            color: #b9ddd4;
            font-size: 14px;
            margin: 28px 16px 10px;
            text-transform: uppercase;
        }

        .menu a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 14px 18px;
            margin-bottom: 5px;
            border-radius: 10px;
            transition: 0.2s;
        }

        .menu a:hover {
            background: #168f72;
        }

        .menu a.active {
            background: #168f72;
        }

        /* CONTENT */
        .main {
            margin-left: 280px;
            min-height: 100vh;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0;
            color: #075f4f;
            font-size: 32px;
        }

        .page-header p {
            color: #666;
            margin-top: 8px;
        }

        /* CARD */
        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .card h2 {
            margin-top: 0;
        }

        /* BUTTON */
        .btn {
            display: inline-block;
            background: #168f72;
            color: white;
            text-decoration: none;
            border: none;
            padding: 11px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #08745b;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #e3f3ed;
            color: #075f4f;
            text-align: left;
            padding: 14px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
        }

        /* STATUS */
        .status-online {
            display: inline-block;
            background: #d9f6e9;
            color: #08754f;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .status-offline {
            display: inline-block;
            background: #fde1e5;
            color: #d32f2f;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        /* DASHBOARD STAT */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
        }

        .stat-card h3 {
            margin: 0;
            color: #666;
            font-size: 15px;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            margin-top: 10px;
            color: #075f4f;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {

            .sidebar {
                width: 230px;
            }

            .main {
                margin-left: 230px;
                padding: 25px;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="logo">

            <img
                src="{{ asset('images/logokomet.png') }}"
                alt="Logo Kota Metro"
            >

            <h2>
                PEMERINTAH KOTA<br>
                METRO
            </h2>

            <p>
                Monitoring Website Kota Metro
            </p>

        </div>


        <div class="menu-title">
            Menu Utama
        </div>


        <div class="menu">

            <a
                href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                Dashboard
            </a>


            <a
                href="{{ route('website.index') }}"
                class="{{ request()->routeIs('website.*') ? 'active' : '' }}"
            >
                Daftar Website
            </a>


            <a
                href="{{ route('monitoring.index') }}"
                class="{{ request()->routeIs('monitoring.*') ? 'active' : '' }}"
            >
                Monitoring
            </a>


            <a
                href="{{ route('riwayat.index') }}"
                class="{{ request()->routeIs('riwayat.*') ? 'active' : '' }}"
            >
                Riwayat Monitoring
            </a>

        </div>


        <div class="menu-title">
            Sistem
        </div>


        <div class="menu">

            <a
                href="{{ route('pengaturan.index') }}"
                class="{{ request()->routeIs('pengaturan.*') ? 'active' : '' }}"
            >
                Pengaturan
            </a>

        </div>

    </aside>


    <!-- ISI HALAMAN -->
    <main class="main">

        @yield('content')

    </main>

</body>
</html>