@extends('layouts.admin')

@section('title', 'Monitoring Website')

@section('content')

<h1>Riwayat Monitoring</h1>
<p>Riwayat hasil pemeriksaan website Kota Metro.</p>

<div class="card">
    <div class="table-header">
        <h2>Riwayat Pemeriksaan Website</h2>

        <form action="{{ route('riwayat.destroy') }}" method="POST"
              onsubmit="return confirm('Yakin ingin menghapus semua log ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="delete">Hapus semua log</button>
        </form>
    </div>

    {{-- PEMBUNGKUS INI YANG MEMBUAT TABEL BISA DIGESER --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Website</th>
                    <th>Status</th>
                    <th>Respons (Detik)</th>
                    <th>Keterangan</th>
                    <th>Waktu Pemeriksaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $log->website->nama_website ?? '-' }}</td>
                    <td>{{ $log->status }}</td>
                    <td>
                        {{ $log->response_time !== null ? number_format($log->response_time / 1000, 2, ',', '.') . ' detik' : '-' }}
                    </td>
                    <td>{{ $log->keterangan ?? '-' }}</td>
                    <td>{{ $log->checked_at?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;">
                        Belum ada riwayat monitoring.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection