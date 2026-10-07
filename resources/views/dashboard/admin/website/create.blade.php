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
        <form
            action="{{ route('website.store') }}"
            method="POST"
        >
            @csrf
            <label>
                Nama Website
            </label>

            <input
                type="text"
                name="nama_website"
                placeholder="Contoh: Portal Kota Metro"
                value="{{ old('nama_website') }}"
                required
            >


            <label for="instansi">
                Instansi
            </label>

            <select name="instansi" id="instansi" required>

                <option value="" disabled {{ old('instansi') ? '' : 'selected' }}>
                    -- Pilih Instansi --
                </option>

                @foreach (config('opd') as $opd)

                    <option
                        value="{{ $opd }}"
                        {{ old('instansi') === $opd ? 'selected' : '' }}
                    >
                        {{ $opd }}
                    </option>

                @endforeach

            </select>


            <label>
                URL Website
            </label>

            <input
                type="url"
                name="url"
                placeholder="https://contoh.go.id"
                value="{{ old('url') }}"
                required
            >

            <div class="buttons">

                <a href="{{ route('dashboard') }}">
                    Kembali
                </a>

                <button type="submit">
                    Simpan Website
                </button>

            </div>
        </form>
    </div>
</div>
</body>
</html>