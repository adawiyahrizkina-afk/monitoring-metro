<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Website</title>
    <link rel="stylesheet" href="/css/dashboard/admin/create.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Edit Website</h1>
            <p class="description">Perbarui informasi website yang dipantau.</p>

            @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            @php
                $pengembang = 'Dinas Komunikasi, Informatika, dan Statistik';
                $namaTerpilih = old('nama_website', $website->nama_website);

                $semuaOpd = collect(config('opd'))->flatten()->all();
                $instansiTerpilih = old('instansi', $website->instansi);
            @endphp

            <form action="{{ route('website.update', $website) }}" method="POST">
                @csrf
                @method('PUT')

                <label for="nama_website">Nama Instansi</label>
                <select name="nama_website" id="nama_website" required>

                    {{-- Data lama yang bukan Diskominfo tetap ditampilkan agar tidak hilang --}}
                    @if ($namaTerpilih && $namaTerpilih !== $pengembang)
                        <option value="{{ $namaTerpilih }}" selected>
                            {{ $namaTerpilih }} (data lama)
                        </option>
                    @endif

                    <option value="{{ $pengembang }}" {{ $namaTerpilih === $pengembang ? 'selected' : '' }}>
                        {{ $pengembang }}
                    </option>
                </select>

                <label for="instansi">OPD / Open Data Kota Metro</label>
                <select name="instansi" id="instansi" required>

                    @if ($instansiTerpilih && !in_array($instansiTerpilih, $semuaOpd))
                        <option value="{{ $instansiTerpilih }}" selected>
                            {{ $instansiTerpilih }} (data lama)
                        </option>
                    @endif

                    @foreach (config('opd') as $kelompok => $daftar)
                        <optgroup label="{{ $kelompok }}">
                            @foreach ($daftar as $opd)
                                <option value="{{ $opd }}" {{ $instansiTerpilih === $opd ? 'selected' : '' }}>
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
                    value="{{ old('url', $website->url) }}"
                    maxlength="255"
                    required>

                <div class="buttons">
                    <a href="{{ route('website.index') }}">Kembali</a>
                    <button type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>