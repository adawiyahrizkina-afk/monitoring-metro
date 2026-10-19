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
                <button type="submit" class="delete" style="width: auto;">Hapus semua log</button>
            </form>
        </div>

    </div>
</div>

<div>
    @forelse ($websites as $website)
    @php($latestIncident = $website['incidents']->first())
    <article class="card" style="margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 16px;">
        <div class="card-child" style="position: relative; justify-content: space-between; padding-bottom: 100px;">
            <a href="{{ $website['url'] }}" style="display: flex; flex-direction: column; gap: 1px;">
                <img src="" alt="favicon"><br>
                <span>{{ $website['name'] }}</span><br>
            </a>
            <div style="margin-top: 20px; width: 100%;">offline
                <!-- {{-- @if($website->status === 'Online')
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
                @endif --}}
            </div> -->
                <div class="interaksi" style="position: absolute; bottom: 0; left: 0; width: 100%; text-align: center;">
                    <button class="btn" style="width: 100%; border-radius: 0;">Detail</button>
                    <button class="delete" style="padding:10px; width: 100%; border-radius: 0 0 7px 7px;">clear</button>
                </div>
            </div>
    </article>
    @empty
    <div class="card" style="grid-column: 1 / -1; text-align: center;">
        {{ $search !== '' ? 'Tidak ada website offline yang cocok dengan pencarian.' : 'Belum ada riwayat website offline.' }}
    </div>
    @endforelse
</div>

<div class="card">
    <div class="card-child" style="position: relative;">
        <div>
            <a href="http://" target="_blank" rel="noopener noreferrer">
                <img src="" alt="logo-favicon" style="text-align: center; width: 100%;"> <br>
                <span>nama-website</span> <br>
                <span>status</span>
            </a>
        </div>

        <div class="interaksi" style="position: absolute; left: 0; bottom: 0; display: flex; justify-content: center; width: 100%;">
            <button class="detail" style="width: 100%; border-radius:  0 0 0 7px;">Detail</button>
            <button class="delete" style="width: 100%; border-radius:  0 0 7px 0;">Clear</button>
        </div>
    </div>
</div>

<div style="margin-top: 20px;">
    {{ $websites->links() }}
</div>
@endsection