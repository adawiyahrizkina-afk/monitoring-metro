@extends('layouts.admin')

@section('title', 'Daftar Website')

@section('content')

<div class="page-header">

    <h1>Daftar Website</h1>

    <p>
        Daftar website yang terdaftar dalam sistem monitoring.
    </p>

</div>


<div class="card">

    <a href="{{ route('website.create') }}" class="btn">
        + Tambah Website
    </a>

    <input
        type="text"
        id="searchWebsite"
        class="search"
        placeholder="Cari website atau instansi..."
        onkeyup="searchTable()">

    <table id="websiteTable">

        <thead>

            <tr>
                <th>No</th>
                <th>Nama Website</th>
                <th>URL</th>
                <th>Status</th>
                <th style="text-align: center;">Interaksi</th>
            </tr>

        </thead>


        <tbody>

            @forelse($websites ?? [] as $website)

            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $website->nama_website ?? '-' }}
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

                    @if(($website->status ?? '') == 'Online')

                    <span class="status-online">
                        ● ONLINE
                    </span>

                    @elseif(($website->status ?? '') == 'Offline')

                    <span class="status-offline">
                        ● OFFLINE
                    </span>

                    @elseif(($website->status ?? '') == 'Warning')

                    <span class="status status-warning">
                        ● WARNING
                    </span>

                    @else

                    <span class="status-unchecked">
                        BELUM DICEK
                    </span>

                    @endif

                </td>

                <td class="button">
                    <a href="{{ route('website.edit', $website) }}"><button class="edit">Edit</button></a>
                    <form action="{{ route('website.destroy', $website) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus website ini? Riwayat monitoringnya juga akan terhapus.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete">Hapus</button>
                    </form>
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

                <td colspan="5" style="text-align:center;">
                    Belum ada website yang terdaftar.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

<script src="/js/search.js"></script>

@endsection