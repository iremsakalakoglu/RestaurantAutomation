<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Perk - Üye Ol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/imask"></script>
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
            min-height: 100vh;
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
            max-width: 500px;
            z-index: 1;
            margin: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .input-row > div {
            flex: 1;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
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

        .login-link {
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

        .password-container {
            position: relative;
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            padding: 5px;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #d4a373;
        }

        .input-error {
            border-color: #ef4444 !important;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
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

    <h1>Üye Ol</h1>

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

    <form action="{{ route('auth.register') }}" method="POST">
        @csrf
        <div class="input-row">
            <div class="input-group">
                <input type="text" name="name" placeholder="Ad" required>
            </div>
            <div class="input-group">
                <input type="text" name="lastName" placeholder="Soyad" required>
            </div>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="E-posta" required autocomplete="email">
        </div>
        <div class="input-group">
            <input type="tel" name="phone" id="phone" placeholder="Telefon (5XX XXX XX XX)" pattern="[0-9]{10}" maxlength="10">
        </div>
        <div class="input-group">
            <div class="password-container">
                <input type="password" name="password" id="password" placeholder="Şifre (En az 8 karakter)" required minlength="8" autocomplete="new-password">
                <i class="fa-solid fa-eye password-toggle" id="passwordToggle"></i>
            </div>
            <small class="text-gray-500 block mt-1">Şifreniz en az 8 karakter olmalıdır</small>
            <small class="error-message" id="passwordError"></small>
        </div>
        <div class="input-group">
            <div class="password-container">
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Şifre Tekrar" required minlength="8" autocomplete="new-password">
                <i class="fa-solid fa-eye password-toggle" id="confirmPasswordToggle"></i>
            </div>
            <small class="error-message" id="confirmPasswordError"></small>
        </div>
        <button type="submit" class="button">Üye Ol</button>
    </form>

    <div class="login-link">
        Zaten hesabınız var mı? <a href="{{ route('auth.login') }}">Giriş Yapın</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var phoneInput = document.getElementById('phone');
    var phoneMask = IMask(phoneInput, {
        mask: '0000000000',
        prepare: function(str) {
            return str.replace(/\D/g, '');
        }
    });

    var passwordInput = document.getElementById('password');
    var confirmPasswordInput = document.getElementById('password_confirmation');
    var passwordToggle = document.getElementById('passwordToggle');
    var confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
    var passwordError = document.getElementById('passwordError');
    var confirmPasswordError = document.getElementById('confirmPasswordError');
    var form = document.querySelector('form');

    function togglePasswordVisibility(input, icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    passwordToggle.addEventListener('click', () => togglePasswordVisibility(passwordInput, passwordToggle));
    confirmPasswordToggle.addEventListener('click', () => togglePasswordVisibility(confirmPasswordInput, confirmPasswordToggle));

    function checkPasswordMatch() {
        var password = passwordInput.value;
        var confirmPassword = confirmPasswordInput.value;

        if (password.length < 8) {
            passwordInput.classList.add('input-error');
            passwordError.style.display = 'block';
            passwordError.textContent = 'Şifre en az 8 karakter olmalıdır';
            return false;
        } else {
            passwordInput.classList.remove('input-error');
            passwordError.style.display = 'none';
        }

        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordInput.classList.add('input-error');
            confirmPasswordError.style.display = 'block';
            confirmPasswordError.textContent = 'Şifreler eşleşmiyor';
            return false;
        } else {
            confirmPasswordInput.classList.remove('input-error');
            confirmPasswordError.style.display = 'none';
        }

        return true;
    }

    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    form.addEventListener('submit', function(e) {
        if (!checkPasswordMatch()) {
            e.preventDefault();
            return;
        }

        var phoneNumber = phoneInput.value.replace(/\D/g, '');
        if (phoneNumber && phoneNumber.length !== 10) {
            e.preventDefault();
            Swal.fire({
                title: 'Hata!',
                text: 'Telefon numarası 10 haneli olmalıdır.',
                icon: 'error',
                confirmButtonColor: '#d4a373'
            });
            return;
        }
    });

    phoneInput.addEventListener('input', function() {
        var number = this.value.replace(/\D/g, '');
        if (number.length === 10) {
            this.value = number.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
        }
    });
});
</script>

</body>
</html>
