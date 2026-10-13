@extends('layouts.admin')

@section('title', 'Riwayat Website Offline')

@section('content')

@if (session('success'))
<div class="card" role="status">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="table-header" style="flex-wrap: wrap; gap: 12px;">
        <h1>Riwayat Website Offline</h1>
        <p>Daftar website yang pernah terdeteksi offline.</p>

        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; flex-direction: row;">
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
            <form action="{{ route('riwayat.destroy') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua log?');" style="max-width: 100%; float:;">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete">Hapus semua log</button>
            </form>
        </div>

    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 16px;">
    @forelse ($websites as $website)
    @php($latestIncident = $website['incidents']->first())
    <article class="card" style="margin: 0;">
        @if ($website['url'])
        <a href="{{ $website['url'] }}" target="_blank" rel="noopener noreferrer" class="url" title="{{ $website['url'] }}">
            {{ $website['name'] }}
        </a>
        @else
        <h2>{{ $website['name'] }}</h2>
        @endif

        <p><span class="status-offline">Offline</span></p>
        <p>{{ $website['incidents']->count() }} kali terdeteksi offline</p>
        <p>
            Terakhir offline:
            {{ $latestIncident->checked_at?->format('d/m/Y H:i:s') ?? '-' }}
        </p>

        <details>
            <summary class="btn" style="display: inline-block; position: static; width: auto; cursor: pointer;">Detail</summary>
            <div style="margin-top: 12px; overflow-wrap: anywhere;">
                <h3>Waktu website offline</h3>
                @foreach ($website['incidents'] as $incident)
                <div style="padding: 10px 0; border-top: 1px solid #e5e9e7;">
                    <p><strong>{{ $incident->checked_at?->format('d/m/Y H:i:s') ?? '-' }}</strong></p>
                    <p>{{ $incident->keterangan ?: 'Website tidak dapat diakses.' }}</p>
                    @if ($incident->response_time !== null)
                    <p>Waktu respons: {{ number_format($incident->response_time, 0, ',', '.') }} ms</p>
                    @endif
                </div>
                @endforeach
            </div>
        </details>
    </article>
    @empty
    <div class="card" style="grid-column: 1 / -1; text-align: center;">
        {{ $search !== '' ? 'Tidak ada website offline yang cocok dengan pencarian.' : 'Belum ada riwayat website offline.' }}
    </div>
    @endforelse
</div>

<div style="margin-top: 20px;">
    {{ $websites->links() }}
</div>
@endsection