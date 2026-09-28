<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Monitoring Website Kota Metro')</title>

    <link rel="stylesheet" href="/css/layouts/layouts.css">
</head>

<body>

    <!-- SIDEBAR -->    
    <aside class="sidebar">

        <div class="logo">

            <img src="{{ asset('images/logokomet.png') }}"
                alt="Logo Kota Metro"
                class="logo-metro">

            <h2>PEMERINTAH KOTA<br>METRO</h2>
            <p>Monitoring Website Kota Metro</p>

        </div>

        <div class="menu-title">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                <path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" />
            </svg>
            <span>
            Menu Utama
            </span>
        </div>

        <div class="menu">
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" id="dashboard">
                Dashboard
            </a>

            <a href="{{ route('website.index') }}"
                class="{{ request()->routeIs('website.index') ? 'active' : '' }}" id="daftar">
                Daftar Website
            </a>

            <a href="{{ route('monitoring.index') }}"
                class="{{ request()->routeIs('monitoring.index') ? 'active' : '' }}" id="monitoring">
                Monitoring
            </a>

            <a href="{{ route('riwayat.index') }}"
                class="{{ request()->routeIs('riwayat.index') ? 'active' : '' }}" id="riwayat">
                Riwayat Monitoring
            </a>
        </div>

        <div class="menu-title">Sistem</div>

        <div class="menu">
            <a href="{{ route('pengaturan.index') }}"
                class="{{ request()->routeIs('pengaturan.index') ? 'active' : '' }}" id="pengaturan">
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


    <!-- ISI HALAMAN -->
    <main class="main">

        @yield('content')

    </main>

</body>

</html>