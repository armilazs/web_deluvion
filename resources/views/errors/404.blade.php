<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Halaman Tidak Ditemukan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .card {
            max-width: 460px;
            padding: 32px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            text-align: center;
        }

        h1 {
            font-size: 56px;
            margin: 0;
            color: #2563eb;
        }

        h2 {
            margin: 12px 0;
        }

        p {
            color: #6b7280;
            line-height: 1.6;
        }

        a {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 18px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>404</h1>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>URL yang diakses tidak tersedia atau sudah dipindahkan.</p>
        <a href="{{ route('login') }}">Kembali ke Login</a>
    </div>
</body>

</html>