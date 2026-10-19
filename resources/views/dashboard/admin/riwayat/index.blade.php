@extends('layouts.admin')

@section('title', 'Riwayat Gangguan Website')

@section('content')

@if (session('success'))
<div class="card" role="status">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-header" style="flex-wrap: wrap; gap: 12px;">
        <div>
            <h1>Riwayat Gangguan Website</h1>
            <p>Laporan seluruh kejadian Warning dan Down, disimpan tanpa batas waktu.</p>
        </div>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <form action="{{ route('riwayat.index') }}" method="GET" style="display: flex; gap: 8px; max-width: 100%;">
                <input
                    type="search"
                    name="search"
                    aria-label="Cari website"
                    value="{{ $search }}"
                    placeholder="Cari nama website..."
                    class="search">
                <button type="submit" class="btn">Cari</button>
                @if ($search !== '')
                <a href="{{ route('riwayat.index') }}" class="btn">Reset</a>
                @endif
            </form>
            <form action="{{ route('riwayat.destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua log?');" style="max-width: 100%;">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete" style="width: auto;">Hapus semua log</button>
            </form>
        </div>
    </div>
</div>

<div class="incident-website-list card" style="display: grid; gap: 10px; grid-template-columns: repeat(4, 1fr);">
    @forelse ($websites as $website)
    <article class="card-child incident-website-card">
        <div class="incident-website-heading">
            <img
                src="{{ $website->favicon_url }}"
                alt=""
                class="website-favicon"
                onerror="this.hidden = true">
            <div>
                <h2>{{ $website->nama_website }}</h2>
                <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer" class="url">{{ $website->url }}</a>
            </div>
        </div>

        <p>
            Gangguan terakhir:
            <span class="status {{ $website->latest_incident_status === 'Warning' ? 'status-warning' : 'status-offline' }}">
                {{ $website->latest_incident_status === 'Warning' ? 'WARNING' : 'DOWN' }}
            </span>
            {{ \Illuminate\Support\Carbon::parse($website->latest_incident_at)->format('d/m/Y H:i:s') }}
        </p>
        <p>{{ number_format($website->incident_count) }} laporan gangguan tersimpan.</p>
        <a href="{{ route('riwayat.website', $website) }}" class="btn">Lihat detail laporan</a>
    </article>
    @empty
    <div class="card incident-empty">
        {{ $search !== '' ? 'Tidak ada website dengan laporan gangguan yang cocok dengan pencarian.' : 'Belum ada laporan Warning atau Down.' }}
    </div>
    @endforelse
</div>

<div style="margin-top: 20px;">
    {{ $websites->links() }}
</div>

@endsection
