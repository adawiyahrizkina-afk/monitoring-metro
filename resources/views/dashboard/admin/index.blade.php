@extends('layouts.admin')

@section('title', 'Dashboard Monitoring Website Kota Metro')

@section('content')

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
                    onkeyup="searchTable()">
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
                                    class="action-form">
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

<!-- SEARCH -->
<script src="/js/admin.js"></script>
@endsection