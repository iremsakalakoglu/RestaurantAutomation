<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Perk - Ana Sayfa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            margin-bottom: 15px;
        }

        .advantages {
            font-size: 14px;
            color: #555;
            text-align: left;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .button {
            background-color: #d4a373;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            display: block;
            width: 100%;
            max-width: 250px;
            text-align: center;
        }

        .button:hover {
            background-color: #c48c63;
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
        Central<sup><i class="fa-solid fa-mug-saucer"></i></sup>Perk
        <span>cafe</span>
    </a>
    <h1>Central Perk'e Hoş Geldiniz</h1>
    <div class="advantages">
        <p>✔ Özel indirimlerden faydalanın</p>
        <p>✔ Hızlı sipariş imkanı</p>
        <p>✔ Yeni ürünlerden ilk siz haberdar olun</p>
        <p>✔ Sadakat programı ile puan kazanın</p>
        <p>✔ Üyelere özel ücretsiz tatlı günleri</p>
    </div>
    <div class="button-container">
        <a href="{{ route('menu') }}" class="button">Menüye Git</a>
        <a href="{{ route('auth.login') }}" class="button">Giriş Yap</a>
        <a href="{{ route('auth.register') }}" class="button">Üye Ol</a>
    </div>
</div>

</body>
</html>
