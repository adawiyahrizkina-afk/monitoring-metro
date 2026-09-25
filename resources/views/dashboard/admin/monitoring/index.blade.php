@extends('layouts.admin')

@section('title', 'Monitoring Website')

@section('content')

<div class="page-header">

    <h1>Monitoring Website</h1>

    <p>
        Melakukan pengecekan status website Pemerintah Kota Metro.
    </p>

</div>


<div class="card">

    <h2>Monitoring Website Kota Metro</h2>

    <p>
        Gunakan fitur pengecekan untuk mengetahui apakah
        website dapat diakses atau tidak.
    </p>


    <form action="{{ route('website.check.all') }}" method="POST">

        @csrf

        <button type="submit" class="btn">
            ✓ Cek Semua Website
        </button>

    </form>

</div>


<div class="card">

    <h2>Hasil Monitoring</h2>

    <table>

        <thead>

            <tr>
                <th>No</th>
                <th>Website</th>
                <th>URL</th>
                <th>Status</th>
                <th>Response</th>
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

                        @if(($website->status ?? '') == 'ONLINE')

                            <span class="status-online">
                                ● ONLINE
                            </span>

                        @else

                            <span class="status-offline">
                                ● OFFLINE
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $website->response_time ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" style="text-align:center;">
                        Belum ada hasil monitoring.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection