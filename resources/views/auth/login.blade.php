<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Login Admin</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #e8f5e9;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 400px;

            background: white;

            padding: 35px;

            border-radius: 12px;

            box-shadow:
                0 5px 20px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        label {
            display: block;

            margin-bottom: 7px;

            font-weight: bold;
        }

        input {
            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 7px;

            margin-bottom: 18px;
        }

        button {
            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 7px;

            background: #2e7d32;

            color: white;

            font-size: 16px;

            cursor: pointer;
        }

        button:hover {
            background: #1b5e20;
        }

        .error {
            background: #ffebee;

            color: #c62828;

            padding: 10px;

            border-radius: 6px;

            margin-bottom: 15px;
        }

    </style>

</head>

<body>

<div class="login-box">

    <h1>
        Monitoring Metro
    </h1>

    <div class="subtitle">
        Monitoring Website Kota Metro
    </div>

    @if(session('error'))

        <div class="error">
            {{ session('error') }}
        </div>

    @endif

    <form
        action="{{ route('login.process') }}"
        method="POST"
    >

        @csrf

        <label>
            Email
        </label>

        <input
            type="email"
            name="email"
            placeholder="Masukkan email"
            required
        >

        <label>
            Password
        </label>

        <input
            type="password"
            name="password"
            placeholder="Masukkan password"
            required
        >

        <button type="submit">
            LOGIN
        </button>

    </form>

</div>

</body>

</html>