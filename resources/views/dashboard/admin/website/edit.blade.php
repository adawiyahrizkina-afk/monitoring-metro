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

            <form action="{{ route('website.update', $website) }}" method="POST">
                @csrf
                @method('PUT')

                <label for="nama_website">Nama Website</label>
                <input
                    id="nama_website"
                    type="text"
                    name="nama_website"
                    value="{{ old('nama_website', $website->nama_website) }}"
                    maxlength="100"
                    required>

                <label for="instansi">Instansi</label>
                <input
                    id="instansi"
                    type="text"
                    name="instansi"
                    value="{{ old('instansi', $website->instansi) }}"
                    maxlength="100"
                    required>

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