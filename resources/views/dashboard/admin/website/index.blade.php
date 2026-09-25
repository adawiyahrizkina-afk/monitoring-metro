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


    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Nama Website</th>
                <th>URL</th>
                <th>Status</th>
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
                    {{ $website->url ?? '-' }}
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

                    <span class="status-warning">
                        ● WARNING
                    </span>

                    @else

                    <span class="status-unchecked">
                        BELUM DICEK
                    </span>

                    @endif

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="4" style="text-align:center;">
                    Belum ada website yang terdaftar.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection