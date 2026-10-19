@extends('layouts.admin')

@section('title', 'Detail Riwayat Gangguan')

@section('content')

<div class="card">
    <div class="incident-website-heading">
        <img
            src="{{ $website->favicon_url }}"
            alt=""
            class="website-favicon"
            onerror="this.hidden = true">
        <div>
            <h1>{{ $website->nama_website }}</h1>
            <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer" class="url">{{ $website->url }}</a>
        </div>
    </div>
    <p>Semua pemeriksaan yang mendeteksi Warning atau Down. Laporan lama tetap tersimpan dan dapat ditelusuri melalui halaman berikutnya.</p>
    <a href="{{ route('riwayat.index') }}" class="btn">Kembali ke riwayat</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Waktu pemeriksaan</th>
                    <th>Status</th>
                    <th>Waktu respons</th>
                    <th>Detail laporan</th>
                    <th>URL yang diperiksa</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->checked_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <span class="status {{ $log->status === 'Warning' ? 'status-warning' : 'status-offline' }}">
                            {{ $log->status === 'Warning' ? 'WARNING' : 'DOWN' }}
                        </span>
                    </td>
                    <td>
                        {{ $log->response_time === null ? '-' : number_format($log->response_time / 1000, 2, ',', '.') . ' detik' }}
                    </td>
                    <td>{{ $log->keterangan ?: 'Tidak ada keterangan.' }}</td>
                    <td>
                        @if ($log->url)
                        <a href="{{ $log->url }}" target="_blank" rel="noopener noreferrer" class="url">{{ $log->url }}</a>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada laporan gangguan untuk website ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $logs->links() }}
    </div>
</div>

@endsection
