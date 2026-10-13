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

<div>
    @forelse ($websites as $website)
    @php($latestIncident = $website['incidents']->first())
    <article class="card" style="margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 16px;">
        <div class="card-child" style="position: relative; justify-content: space-between; padding-bottom: 100px; text-align: left;" >
            <a href="#urlwebsite">
                <img src="" alt="favicon"><br>
                <span>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Repudiandae recusandae aspernatur id praesentium veritatis quos dolore placeat maiores, perspiciatis ab iure totam fuga similique quod vel adipisci. Saepe, natus? Sit. Lorem ipsum dolor sit amet consectetur, adipisicing elit. Autem accusamus voluptas soluta ducimus cum natus itaque laborum suscipit et quos adipisci totam, ratione alias, cumque maxime optio sint quod. Ipsum?</span><br>
            </a>
            <div style="margin-top: 20px; width: 100%; text-align: center;">status</div>
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
@endsection