<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name ?? 'Restaurant' }} - Giriş</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            position: relative;
            background-image: url('/images/login_bg.jpg');
            background-size: cover;
            background-position: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        .container {
            position: relative;
            background-color: rgba(243, 227, 211, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 80%;
            max-width: 400px;
            z-index: 1;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
            font-size: 14px;
        }

        .button {
            background-color: #d4a373;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }

        .button:hover {
            background-color: #c48c63;
        }

        .register-link {
            margin-top: 15px;
            font-size: 14px;
        }

        a {
            color: #d4a373;
            text-decoration: none;
        }

        a:hover {
            color: #c48c63;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .logo i {
            color: #d4a373;
        }

        .logo span {
            font-size: 18px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="container">
    <a href="{{ route('menu') }}" class="logo">
    {{ $settings->name ?? 'Restaurant' }}<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>
        <span>cafe</span>
    </a>

    <h1>Giriş Yap</h1>

    @if(session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ session('success') ? 'Başarılı!' : 'Hata!' }}',
                    text: '{{ session('success') ?? session('error') }}',
                    icon: '{{ session('success') ? 'success' : 'error' }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            });
        </script>
    @endif

    <form action="{{ route('auth.login') }}" method="POST">
        @csrf
        <div class="input-group">
            <input type="email" name="email" placeholder="E-posta" required autocomplete="email">
        </div>
        <div class="input-group">
            <input type="password" name="password" placeholder="Şifre" required autocomplete="current-password">
        </div>
        <div class="remember-forgot">
            <label>
                <input type="checkbox" name="remember"> Beni Hatırla
            </label>
            <!-- <a href="#">Şifremi Unuttum</a> -->
        </div>
        <button type="submit" class="button">Giriş Yap</button>
    </form>

    <div class="register-link">
        Hesabınız yok mu? <a href="{{ route('auth.register') }}">Üye Olun</a>
    </div>
</div>

</body>
</html>
