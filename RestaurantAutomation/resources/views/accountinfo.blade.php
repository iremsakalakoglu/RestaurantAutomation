<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Perk - Hesap Bilgilerim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Toast Mesajı -->
    <div id="toast-message" class="fixed top-20 right-6 z-50 min-w-[220px] max-w-xs px-4 py-3 rounded-lg shadow-lg text-white text-center font-semibold transition-all duration-500 opacity-0 pointer-events-none"></div>
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk <span class="text-gray-600 text-lg">cafe</span>
            </div>
            <!-- Hamburger menu button (mobile only) -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <!-- Menü ve kullanıcı (masaüstü) -->
            <div class="hidden md:flex items-center gap-4">
                <span class="text-gray-600">Menü</span>
                <span class="text-gray-600">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            </div>
        </div>
        <!-- Mobil açılır menü -->
        <div id="mobile-menu" class="md:hidden hidden flex-col gap-2 bg-[#f5e6d3] px-4 py-4 rounded-b-lg shadow-lg">
            <span class="block py-2 text-lg font-semibold text-gray-700">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            <div class="border-t border-[#e5d5c0] my-2"></div>
            <a href="{{ route('menu') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Menü</a>
            <a href="{{ route('account.info') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Hesap Bilgilerim</a>
            <a href="{{ route('order.history') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Geçmiş Siparişlerim</a>
            <a href="{{ route('favorites') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Favorilerim</a>
            <a href="{{ route('notifications') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Bildirimlerim</a>
            <a href="#" class="block py-2 text-gray-700 hover:text-[#d4a373]">Destek/Yardım</a>
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
            <a href="{{ route('order.history') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-clock-rotate-left"></i> Geçmiş Siparişlerim
            </a>
            <a href="{{ route('favorites') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-heart"></i> Favorilerim
            </a>
            <a href="{{ route('notifications') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-bell"></i> Bildirimlerim
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-circle-question"></i> Destek/Yardım
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
                            <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone ?? '' }}" class="w-44 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition" maxlength="14" placeholder="5xx xxx xx xx">
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
    <script>
        var phoneMask = IMask(
            document.getElementById('phone'),
            {
                mask: '(000) 000 00 00'
            }
        );

        // Form değişikliklerini takip et ve ana güncelle butonunu aktif et
        const form = document.getElementById('account-info-form');
        const mainUpdateBtn = document.getElementById('main-update-btn');
        const inputElements = Array.from(form.querySelectorAll('input, select, textarea'));
        const initialData = inputElements.map(el => el.value);

        form.addEventListener('input', function() {
            const changed = inputElements.some((el, i) => el.value !== initialData[i]);
            if (changed) {
                mainUpdateBtn.disabled = false;
                mainUpdateBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                mainUpdateBtn.classList.add('bg-[#d4a373]', 'hover:bg-[#c48c63]', 'cursor-pointer');
            } else {
                mainUpdateBtn.disabled = true;
                mainUpdateBtn.classList.add('bg-gray-300', 'cursor-not-allowed');
                mainUpdateBtn.classList.remove('bg-[#d4a373]', 'hover:bg-[#c48c63]', 'cursor-pointer');
            }
        });

        // Toast mesajı fonksiyonu
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-message');
            toast.textContent = message;
            toast.className = `fixed top-20 right-6 z-50 min-w-[220px] max-w-xs px-4 py-3 rounded-lg shadow-lg text-white text-center font-semibold transition-all duration-500 opacity-0 pointer-events-none`;
            if (type === 'success') {
                toast.classList.add('bg-green-500');
            } else {
                toast.classList.add('bg-red-500');
            }
            toast.style.opacity = '1';
            toast.style.pointerEvents = 'auto';
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.pointerEvents = 'none';
            }, 3000);
        }

        // Blade'den session mesajı varsa göster
        @if(session('success'))
            showToast(@json(session('success')), 'success');
        @endif
        @if(session('password_success'))
            showToast(@json(session('password_success')), 'success');
        @endif
        @if(session('password_error'))
            showToast(@json(session('password_error')), 'error');
        @endif

        // Hamburger menü aç/kapat
        document.getElementById('mobile-menu-btn').onclick = function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html> 