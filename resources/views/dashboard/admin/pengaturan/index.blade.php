@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('content')

<div class="page-header">

    <h1>Pengaturan</h1>

    <p>
        Pengaturan sistem monitoring website Kota Metro.
    </p>

</div>


<div class="card">

    <h2>Pengaturan Sistem</h2>

    <p>
        Halaman ini digunakan untuk mengatur konfigurasi
        sistem monitoring website.
    </p>


    <hr>


    <h3>Informasi Sistem</h3>

    <p>
        <b>Nama Sistem:</b>
        Monitoring Website Kota Metro
    </p>

    <p>
        <b>Platform:</b>
        Laravel
    </p>

    <p>
        <b>Status Sistem:</b>

        <span class="status-online">
            ● AKTIF
        </span>

    </p>

</div>

@endsection