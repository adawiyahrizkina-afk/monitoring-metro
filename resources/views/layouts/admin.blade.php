<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">\
    <title>@yield('title', 'Monitoring Website Kota Metro')</title>
    <link rel="stylesheet" href="/css/layouts/layouts.css">
    <link rel="shortcut icon" href="/images/logokomet.png" type="image/x-icon">
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <button type="button" class="sidebar-toggle" onclick="toggleSidebar()">
            <svg id="sidebar_off" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white">
                <path d="M440-760v-80h80v80h-80Zm0 640v-80h80v80h-80ZM280-760v-80h80v80h-80Zm0 640v-80h80v80h-80ZM120-760v-80h80v80h-80Zm0 640v-80h80v80h-80Zm480 0v-80h80v-560h-80v-80h240v80h-80v560h80v80H600ZM280-320 120-480l160-160 56 56-63 64h287v80H273l63 64-56 56Z" />
            </svg>
            <svg id="sidebar_on" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="white">
                <path d="m680-320-56-56 63-64H440v-80h247l-63-64 56-56 160 160-160 160ZM200-200h80v-560h-80v560Zm-80 80v-720h240v720H120Zm320 0v-80h80v80h-80Zm0-640v-80h80v80h-80Zm160 640v-80h80v80h-80Zm0-640v-80h80v80h-80Zm160 640v-80h80v80h-80Zm0-640v-80h80v80h-80ZM200-200h80-80Z" />
            </svg>
        </button>
        </button>

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
                <div class="menu-icon">
                    Dashboard
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M600-160v-280h280v280H600ZM440-520v-280h440v280H440ZM80-160v-280h440v280H80Zm0-360v-280h280v280H80Zm440-80h280v-120H520v120ZM160-240h280v-120H160v120Zm520 0h120v-120H680v120ZM160-600h120v-120H160v120Zm360 0Zm-80 240Zm240 0ZM280-600Z" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('website.index') }}"
                class="{{ request()->routeIs('website.index') ? 'active' : '' }}" id="daftar">
                <div class="menu-icon">
                    Daftar Website
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M320-320h480v-400H320v400Zm0 80q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Zm160-720v480-480Z" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('monitoring.index') }}"
                class="{{ request()->routeIs('monitoring.index') ? 'active' : '' }}" id="monitoring">
                <div class="menu-icon">
                    Monitoring
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M200-280h560q33 0 56.5-23.5T840-360v-240q0-33-23.5-56.5T760-680H200q-33 0-56.5 23.5T120-600v240q0 33 23.5 56.5T200-280Zm80 120v-40h-80q-66 0-113-47T40-360v-240q0-66 47-113t113-47h560q66 0 113 47t47 113v240q0 66-47 113t-113 47h-80v40H280Zm200-320Z" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('riwayat.index') }}"
                class=" {{ request()->routeIs('riwayat.index') ? 'active' : '' }}" id="riwayat">
                <div class="menu-icon">
                    Riwayat Monitoring
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M480-80q-155 0-269-103T82-440h81q15 121 105.5 200.5T480-160q134 0 227-93t93-227q0-134-93-227t-227-93q-86 0-159.5 42.5T204-640h116v80H88q29-140 139-230t253-90q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm112-232L440-464v-216h80v184l128 128-56 56Z" />
                    </svg>
                </div>
            </a>
        </div>

        <div class="menu-title">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                <path d="M80-680v-80q0-33 23.5-56.5T160-840h640q33 0 56.5 23.5T880-760v80h-80v-80H160v80H80Zm240 560v-80H160q-33 0-56.5-23.5T80-280v-80h80v80h640v-80h80v80q0 33-23.5 56.5T800-200H640v80H320Zm160-400Zm-288 0 104-104-56-56L80-520l160 160 56-56-104-104Zm576 0L664-416l56 56 160-160-160-160-56 56 104 104Z" />
            </svg>
            <span>
                Sistem
            </span>
        </div>

        <div class="menu">
            <a href="{{ route('pengaturan.index') }}"
                class="{{ request()->routeIs('pengaturan.index') ? 'active' : '' }}" id="pengaturan">
                <div class="menu-icon">
                    Pengaturan
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z" />
                    </svg>
                </div>
            </a>
        </div>
        <div class="logout">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">
                    <div class="menu-icon">
                        Logout
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                            <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h280v80H200v560h280v80H200Zm440-160-55-58 102-102H360v-80h327L585-622l55-58 200 200-200 200Z" />
                        </svg>
                    </div>
                </button>
            </form>
        </div>
    </aside>


    <!-- ISI HALAMAN -->
    <main class="main">

        @yield('content')

    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const sdbaroff = document.getElementById('sidebar_off');
            const sdbaron = document.getElementById('sidebar_on');

            sidebar.classList.toggle('collapsed');

            if (sidebar.classList.contains('collapsed')) {
                localStorage.setItem('sidebarStatus', 'collapsed');
            } else {
                localStorage.setItem('sidebarStatus', 'expanded');
            }

            if (left === 0){
                sdbaroff.style.display = 'flex';
                sdbaron.style.display = 'none';
            }else{
                sdbaroff.style.display = 'none';
                sdbaron.style.display = 'flex';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');

            const sidebarStatus = localStorage.getItem('sidebarStatus');

            if (sidebarStatus === 'collapsed') {
                sidebar.classList.add('collapsed');
            }
        });
    </script>

</body>

</html>