<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Website</title>
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f8f6;
        }

        .container {
            width: 600px;
            max-width: 90%;
            margin: 60px auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }

        h1 {
            color: #064e3b;
            margin-top: 0;
        }

        .description {
            color: #64748b;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #334155;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #16865f;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        button,
        a {
            padding: 11px 18px;
            border-radius: 7px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        button {
            background: #16865f;
            color: white;
        }

        a {
            background: #e2e8f0;
            color: #334155;
        }

    </style>
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


            <label>
                Instansi
            </label>

            <input
                type="text"
                name="instansi"
                placeholder="Contoh: Pemerintah Kota Metro"
                value="{{ old('instansi') }}"
                required
            >


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