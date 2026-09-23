<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Monitoring Website Kota Metro</title>
    <link rel="stylesheet" href="/css/index.css">
</head>


<body>


    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">
        <h1 style="display: flex; align-items: center; gap: 10px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/LOGO_KOTA_METRO.png?utm_source=id.wikipedia.org&utm_campaign=index&utm_content=original" alt="logo" width="50" style="object-fit: cover;">
            judul
        </h1>
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
            class="menu active">
            &nbsp; Dashboard
        </a>


        <a
            href="#daftar-website"
            class="menu">
            &nbsp; Daftar Website
        </a>


        <a
            href="#daftar-website"
            class="menu">
            &nbsp; Monitoring
        </a>


        <a
            href="#"
            class="menu">
            &nbsp; Riwayat Monitoring
        </a>


        <div class="menu-title">
            SISTEM
        </div>


        <a
            href="#"
            class="menu">
            &nbsp; Pengaturan
        </a>


        <div class="sidebar-bottom">

            <form
                action="{{ route('logout') }}"
                method="POST">

                @csrf

                <button
                    type="submit"
                    class="menu"
                    style="
                    width:100%;
                    text-align:left;
                    background-color: var(--merah);
                    color: var(--kuning);
                ">

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
                        method="POST">

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-green">
                            Cek Semua Website
                        </button>

                    </form>

                    <a
                        href="#"
                        class="btn btn-white">
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
                id="daftar-website">
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
                        placeholder="Cari website atau instansi...">
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
                                    class="url">

                                    {{ $website->url }}
                                </a>
                            </td>
                            <td>
                                @if($website->status == 'Online')

                                <span
                                    class="status status-online">

                                    <span
                                        class="status-dot dot-online"></span>

                                    ONLINE

                                </span>

                                @else

                                <span
                                    class="status status-offline">

                                    <span
                                        class="status-dot dot-offline"></span>

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
                                    method="POST">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="check-btn">
                                        Cek
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td
                                colspan="8"
                                class="empty">
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

        search.addEventListener('keyup', function() {

            const keyword =
                this.value.toLowerCase();

            const rows =
                document.querySelectorAll(
                    '#websiteTable tr'
                );

            rows.forEach(function(row) {
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