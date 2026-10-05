@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('content')

<div class="page-header">

    <h1>Pengaturan</h1>

    <p>
        Pengaturan sistem monitoring website Kota Metro.
    </p>

</div>

@if(session('success'))
<div class="alert">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert alert-error">
    {{ $errors->first() }}
</div>
@endif


<div class="card">

    <h2>Retensi Riwayat Monitoring</h2>

    <p>
        Tentukan kapan riwayat monitoring yang lama dihapus otomatis.
    </p>

    <form action="{{ route('pengaturan.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
        @csrf

        <label for="retention_days">Hapus riwayat setelah</label>
        <select id="retention_days" name="retention_days" class="search" required>
            <option value="0" @selected($retentionDays===0)>Tidak pernah</option>
            <option value="1" @selected($retentionDays===1)>1 hari</option>
            <option value="7" @selected($retentionDays===7)>1 minggu</option>
            <option value="30" @selected($retentionDays===30)>1 bulan</option>
            <option value="365" @selected($retentionDays===365)>1 tahun</option>
        </select><br>

        <label for="website_list_interval_seconds">Interval timer Daftar Website</label>
        <select id="website_list_interval_seconds" name="website_list_interval_seconds" class="search" required>
            <option value="30" @selected($websiteListIntervalSeconds===30)>30 detik</option>
            <option value="60" @selected($websiteListIntervalSeconds===60)>1 menit</option>
            <option value="120" @selected($websiteListIntervalSeconds===120)>2 menit</option>
            <option value="300" @selected($websiteListIntervalSeconds===300)>5 menit</option>
        </select>
        <p>Timer grafik menunjukkan waktu menuju interval berikutnya. Tombol cek manual tetap dapat digunakan kapan saja.</p>

        <label for="monitoring_interval_seconds">Interval pengecekan halaman Monitoring</label>
        <select id="monitoring_interval_seconds" name="monitoring_interval_seconds" class="search" required>
            <option value="15" @selected($monitoringIntervalSeconds===15)>15 detik</option>
            <option value="30" @selected($monitoringIntervalSeconds===30)>30 detik</option>
            <option value="60" @selected($monitoringIntervalSeconds===60)>1 menit</option>
            <option value="120" @selected($monitoringIntervalSeconds===120)>2 menit</option>
        </select>

        <button type="submit" class="btn btn-check-all">
            Simpan Pengaturan
        </button>
    </form>

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