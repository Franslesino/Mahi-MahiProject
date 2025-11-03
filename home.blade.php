<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | EduGus</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f8f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .logo {
            margin-bottom: 30px;
        }
        .logo img {
            width: 120px;
        }
        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .btn {
            display: block;
            width: 160px;
            margin: 10px auto;
            padding: 10px;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-primary {
            background-color: #006b5b;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #004e42;
        }
        .btn-secondary {
            background-color: #e5e5e5;
            color: #aaa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/eduquest-logo.png') }}" alt="EduQuest Logo">
        </div>
        <h2>Selamat Datang Di EduGus</h2>
        <button class="btn btn-primary" onclick="window.location.href='{{ url('/login') }}'">Masuk</button>
        <button class="btn btn-secondary">Buat</button>
    </div>
</body>
</html>
