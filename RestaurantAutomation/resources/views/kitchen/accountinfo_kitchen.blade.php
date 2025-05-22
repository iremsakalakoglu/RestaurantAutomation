<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Hesap Bilgilerim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                <a href="{{ route('kitchen.dashboard') }}" class="flex items-center gap-1">
                {{ $settings->name ?? 'Restaurant' }}<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup> <span class="text-gray-600 text-lg">Mutfak</span>
                </a>
            </div>
            <!-- Hamburger menu button (mobile only) -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <!-- Menü ve kullanıcı (masaüstü) -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('kitchen.dashboard') }}" class="flex items-center gap-2 text-gray-600 hover:text-[#d4a373] transition-colors font-medium">
                <i class="fa-solid fa-utensils"></i>
                    Mutfak
                </a>
                <span class="text-gray-600">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            </div>
        </div>
        <!-- Mobil açılır menü -->
        <div id="mobile-menu" class="md:hidden hidden flex-col gap-2 bg-[#f5e6d3] px-4 py-4 rounded-b-lg shadow-lg">
            <span class="block py-2 text-lg font-semibold text-gray-700">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            <div class="border-t border-[#e5d5c0] my-2"></div>
            <a href="{{ route('kitchen.dashboard') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Mutfak</a>
            <a href="{{ route('account.info') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Hesap Bilgilerim</a>
            <div class="border-t border-[#e5d5c0] my-2"></div>
            <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="w-full text-left py-2 text-red-500 hover:text-red-700">Çıkış Yap</button>
            </form>
        </div>
    </nav>

    <!-- Sidebar (Sabit) -->
    <aside class="hidden md:flex fixed top-[4.5rem] left-0 h-[calc(100vh-4.5rem)] w-64 bg-[#f5e6d3] p-6 shadow-md rounded-r-2xl flex-col gap-2 z-40">
        <nav class="space-y-2 mt-2">
            <a href="{{ route('account.info') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold bg-white text-[#d4a373] shadow-sm">
                <i class="fa-solid fa-user"></i> Hesap Bilgileri
            </a>
        </nav>
    </aside>

    <!-- Main Content (Sidebar genişliği kadar padding) -->
    <main class="flex flex-col items-center justify-start pt-24 pb-8 px-4 min-h-[calc(100vh-4.5rem)] bg-gray-100 md:pl-64">
        <div class="w-full max-w-5xl flex flex-col md:flex-row gap-12 justify-center items-start">
            <!-- Hesap Bilgilerim -->
            <section class="bg-white rounded-2xl shadow-lg p-6 md:p-10 flex flex-col justify-between border border-[#f5e6d3] w-full max-w-md mx-auto my-10 px-4 flex-1 min-w-0 basis-0">
                <h2 class="text-2xl font-bold text-[#b88b5a] mb-6 tracking-wide">Üyelik Bilgilerim</h2>
                <form id="account-info-form" action="{{ route('account.info.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ad</label>
                            <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Soyad</label>
                            <input type="text" name="lastName" value="{{ Auth::user()->lastName ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                        <input type="email" name="email" value="{{ Auth::user()->email ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cep Telefonu</label>
                        <div class="flex gap-2 items-end">
                            <select class="w-20 px-2 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                                <option selected>+90</option>
                            </select>
                            <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone ?? '' }}" class="w-44 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" maxlength="15" minlength="15" required placeholder="(5xx) xxx xx xx">
                        </div>
                    </div>
                    <button id="main-update-btn" type="submit" class="w-full bg-gray-300 text-white px-4 py-2 rounded-lg font-semibold cursor-not-allowed" disabled>Güncelle</button>
                </form>
            </section>

            <!-- Şifre Güncelleme -->
            <section class="bg-white rounded-2xl shadow-lg p-6 md:p-10 flex flex-col justify-between border border-[#f5e6d3] w-full max-w-md mx-auto my-10 px-4 flex-1 min-w-0 basis-0">
                <h2 class="text-2xl font-bold text-[#b88b5a] mb-6 tracking-wide">Şifre Güncelleme</h2>
                <form action="{{ route('account.password.update') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Şifre</label>
                        <input type="password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre</label>
                        <input type="password" name="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre (Tekrar)</label>
                        <input type="password" name="new_password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" required>
                    </div>
                    <button type="submit" class="w-full bg-[#d4a373] text-white px-4 py-2 rounded-lg hover:bg-[#c48c63] transition-colors font-semibold text-lg shadow">Şifreyi Güncelle</button>
                </form>
            </section>
        </div>
    </main>
    <script src="https://unpkg.com/imask"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var phoneInput = document.getElementById('phone');
        var mainUpdateBtn = document.getElementById('main-update-btn');
        var form = document.getElementById('account-info-form');
        var initialPhone = phoneInput.value;

        // Maskeyi uygula
        var phoneMask = IMask(phoneInput, {
            mask: '(000) 000 00 00'
        });

        // Sayfa yüklendiğinde butonu pasif yap
        mainUpdateBtn.disabled = true;
        mainUpdateBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
        mainUpdateBtn.classList.remove('bg-[#d4a373]', 'hover:bg-[#c48c63]', 'cursor-pointer');

        function checkPhoneChanged() {
            if (phoneInput.value !== initialPhone && phoneInput.value.length === 15) {
                mainUpdateBtn.disabled = false;
                mainUpdateBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                mainUpdateBtn.classList.add('bg-[#d4a373]', 'hover:bg-[#c48c63]', 'cursor-pointer');
            } else {
                mainUpdateBtn.disabled = true;
                mainUpdateBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                mainUpdateBtn.classList.remove('bg-[#d4a373]', 'hover:bg-[#c48c63]', 'cursor-pointer');
            }
        }

        phoneInput.addEventListener('input', checkPhoneChanged);
        window.addEventListener('DOMContentLoaded', checkPhoneChanged);

        form.addEventListener('submit', function(e) {
            if (phoneInput.value.length !== 15) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Telefon numarası eksik veya hatalı. Lütfen tam olarak 11 haneli giriniz.',
                    confirmButtonColor: '#d4a373'
                });
            }
        });

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: @json(session('success')),
                confirmButtonColor: '#d4a373',
                customClass: {
                    popup: 'rounded-xl'
                }
            });
        @endif

        // Hamburger menü aç/kapat
        document.getElementById('mobile-menu-btn').onclick = function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html> 