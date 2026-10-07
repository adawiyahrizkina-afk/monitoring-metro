<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Website</title>
    <link rel="stylesheet" href="/css/dashboard/admin/create.css">
</head>
<body>
<div class="container">

    <div class="card">

        <h1>Tambah Website</h1>

        <p class="description">
            Tambahkan website Pemerintah Kota Metro
            yang ingin dipantau.
        </p>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('website.store') }}" method="POST">
            @csrf

            <label for="nama_website">Nama Instansi</label>
            <select name="nama_website" id="nama_website" required>
                <option value="Dinas Komunikasi, Informatika, dan Statistik" selected>
                    Dinas Komunikasi, Informatika, dan Statistik
                </option>
            </select>

            <label for="instansi">OPD / Open Data Kota Metro</label>
            <select name="instansi" id="instansi" required>

                <option value="" disabled {{ old('instansi') ? '' : 'selected' }}>
                    -- Pilih OPD --
                </option>

                @foreach (config('opd') as $kelompok => $daftar)
                    <optgroup label="{{ $kelompok }}">
                        @foreach ($daftar as $opd)
                            <option value="{{ $opd }}" {{ old('instansi') === $opd ? 'selected' : '' }}>
                                {{ $opd }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach

            </select>

            <label for="url">URL Website</label>
            <input
                id="url"
                type="url"
                name="url"
                placeholder="https://contoh.go.id"
                value="{{ old('url') }}"
                required
            >

            <div class="buttons">
                <a href="{{ route('dashboard') }}">Kembali</a>
                <button type="submit">Simpan Website</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>