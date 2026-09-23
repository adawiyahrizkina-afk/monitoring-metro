<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <section class="container">
        <section class="parent">
            <section class="satu head" id="head">
                <h1>
                    Monitoring Metro
                </h1>

                <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/LOGO_KOTA_METRO.png?utm_source=id.wikipedia.org&utm_campaign=index&utm_content=original" alt="logo">

                <div class="subtitle">
                    Monitoring Website Kota Metro
                </div>
            </section>

            <section class="dua login" id="login">
                <h1>
                    Login
                </h1>
                @if(session('error-login'))

                <div class="error">
                    {{ session('error-login') }}
                </div>

                @endif

                <form
                    action="{{ route('login.process') }}"
                    method="POST">

                    @csrf

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Masukkan email"
                        required>

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required>

                    <button type="submit">
                        LOGIN
                    </button>

                </form>
            </section>


        </section>
    </section>
    <script src="/js/login.js"></script>
</body>

</html>