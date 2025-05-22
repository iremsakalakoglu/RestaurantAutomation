<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Destek/Yardım</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                <a href="{{ route('menu', ['table' => $sessionTableId]) }}" class="flex items-center gap-1">
                    {{ $settings->name ?? 'Restaurant' }}<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup> <span class="text-gray-600 text-lg">cafe</span>
                </a>
            </div>
            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <!-- Menü ve kullanıcı (masaüstü) -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('menu', ['table' => $sessionTableId]) }}" class="text-gray-600 hover:text-[#d4a373] transition-colors">Menü</a>
                <span class="text-gray-600">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            </div>
        </div>
        <div id="mobile-menu" class="md:hidden hidden flex-col gap-2 bg-[#f5e6d3] px-4 py-4 rounded-b-lg shadow-lg">
            <span class="block py-2 text-lg font-semibold text-gray-700">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            <div class="border-t border-[#e5d5c0] my-2"></div>
            <a href="{{ route('menu', ['table' => $sessionTableId]) }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Menü</a>
            <a href="{{ route('account.info') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Hesap Bilgilerim</a>
            <a href="{{ route('order.history') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Geçmiş Siparişlerim</a>
            <a href="{{ route('favorites') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Favorilerim</a>
            <a href="{{ route('notifications') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Bildirimlerim</a>
            <a href="{{ route('support') }}" class="block py-2 text-gray-700 hover:text-[#d4a373] font-semibold bg-white text-[#d4a373] shadow-sm">Destek/Yardım</a>
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
            <a href="{{ route('account.info') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
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
            <a href="{{ route('support') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold bg-white text-[#d4a373] shadow-sm">
                <i class="fa-solid fa-circle-question"></i> Destek/Yardım
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex flex-col items-center justify-start pt-24 pb-8 px-4 min-h-[calc(100vh-4.5rem)] bg-gray-100 md:pl-64">
        <div class="w-full max-w-3xl">
            <h1 class="text-3xl font-bold text-[#b88b5a] mb-8">Destek & Yardım</h1>
            <!-- SSS -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-4 text-[#d4a373]">Sıkça Sorulan Sorular</h2>
                <div class="space-y-3" id="faq-accordion">
                    <div class="bg-white rounded-lg shadow">
                        <button type="button" class="w-full flex justify-between items-center p-4 font-semibold text-gray-800 focus:outline-none faq-question">
                            Sipariş verdikten sonra ek ürün ekleyebilir miyim?
                            <span class="ml-2 transition-transform"><i class="fa-solid fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer px-4 pb-4 text-gray-600 hidden">
                            Evet, siparişinizi verdikten sonra aynı QR kod ile tekrar giriş yapıp yeni ürünler ekleyebilirsiniz. Eklediğiniz ürünler mevcut adisyonunuza eklenir.
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
                        <button type="button" class="w-full flex justify-between items-center p-4 font-semibold text-gray-800 focus:outline-none faq-question">
                            Ödemeyi nasıl yapacağım?
                            <span class="ml-2 transition-transform"><i class="fa-solid fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer px-4 pb-4 text-gray-600 hidden">
                            Kasaya giderek masa numaranız ile adisyonunuzu kasiyere bildirerek kasadan ödemenizi gerçekleştirebilirsiniz. Şu anda sistemimizde online ödeme bulunmamaktadır, ödeme kasadan yapılmaktadır.
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
                        <button type="button" class="w-full flex justify-between items-center p-4 font-semibold text-gray-800 focus:outline-none faq-question">
                            QR kodu okutmadan sipariş vermem mümkün mü?
                            <span class="ml-2 transition-transform"><i class="fa-solid fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer px-4 pb-4 text-gray-600 hidden">
                            Kendiniz sipariş oluşturmak istiyorsanız QR kod okutmadan sipariş vermeniz mümkün değildir. Fakat telefonunuzun girmemesi/ veya sipariş oluşturamamanız durumunda garsondan yardım alarak siparişinizi oluşturabilirsiniz.
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
                        <button type="button" class="w-full flex justify-between items-center p-4 font-semibold text-gray-800 focus:outline-none faq-question">
                            Aynı masadan birden fazla kişi sipariş verebilir mi?
                            <span class="ml-2 transition-transform"><i class="fa-solid fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer px-4 pb-4 text-gray-600 hidden">
                            Evet, aynı masadaki herkes kendi telefonundan QR kodu okutarak sipariş verebilir. Tüm siparişler aynı adisyonda toplanır.
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow">
                        <button type="button" class="w-full flex justify-between items-center p-4 font-semibold text-gray-800 focus:outline-none faq-question">
                            Masa değiştirdim, yeni masada sipariş verebilir miyim?
                            <span class="ml-2 transition-transform"><i class="fa-solid fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer px-4 pb-4 text-gray-600 hidden">
                            Masayı değiştirdiyseniz, yeni masadaki QR kodu okutarak yeni bir adisyon başlatabilirsiniz. Önceki masadaki adisyon için garsondan yardım alabilirsiniz.
                        </div>
                    </div>
                </div>
            </div>
            <!-- İletişim Formu -->
            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 border border-green-300">{{ session('success') }}</div>
            @endif
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-[#d4a373]">Bize Ulaşın</h2>
                <form action="{{ route('support.contact') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="fullname" class="block text-gray-700 font-medium mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                        <input type="text" id="fullname" name="fullname" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d4a373]" required>
                    </div>
                    <div>
                        <span class="block text-gray-700 font-medium mb-1 mb-2">İletişim Tercihi <span class="text-red-500">*</span></span>
                        <div class="flex gap-2 mb-2">
                            <button type="button" id="btn-phone" class="contact-toggle bg-[#d4a373] text-white px-4 py-2 rounded focus:outline-none">Telefon</button>
                            <button type="button" id="btn-email" class="contact-toggle bg-gray-200 text-gray-700 px-4 py-2 rounded focus:outline-none">E-posta</button>
                        </div>
                        <div id="phone-input-wrapper">
                            <label for="phone" class="block text-gray-700 font-medium mb-1">Cep Telefonu <span class="text-red-500">*</span></label>
                            <input type="text" id="phone" name="phone" maxlength="15" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d4a373]" placeholder="(5xx) xxx xx xx">
                        </div>
                        <div id="email-input-wrapper" class="hidden">
                            <label for="email" class="block text-gray-700 font-medium mb-1">E-posta <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d4a373]" placeholder="ornek@mail.com">
                        </div>
                    </div>
                    <div>
                        <label for="subject" class="block text-gray-700 font-medium mb-1">Konu</label>
                        <select id="subject" name="subject" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d4a373]" required>
                            <option value="">Bir konu seçin</option>
                            <option value="Şikayet">Şikayet</option>
                            <option value="Talep">Talep</option>
                            <option value="Bilgi">Bilgi</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                    </div>
                    <div>
                        <label for="message" class="block text-gray-700 font-medium mb-1">Mesajınız <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#d4a373]" required></textarea>
                    </div>
                    <button type="submit" class="bg-[#d4a373] text-white px-6 py-2 rounded hover:bg-[#b88b5a] transition">Gönder</button>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('mobile-menu-btn').onclick = function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
        // FAQ Accordion
        const questions = document.querySelectorAll('.faq-question');
        const answers = document.querySelectorAll('.faq-answer');
        questions.forEach((btn, idx) => {
            btn.addEventListener('click', function() {
                answers.forEach((ans, i) => {
                    if (i === idx) {
                        ans.classList.toggle('hidden');
                        btn.querySelector('i').classList.toggle('rotate-180');
                    } else {
                        ans.classList.add('hidden');
                        questions[i].querySelector('i').classList.remove('rotate-180');
                    }
                });
            });
        });

        // İletişim tercihi butonları
        const btnPhone = document.getElementById('btn-phone');
        const btnEmail = document.getElementById('btn-email');
        const phoneInputWrapper = document.getElementById('phone-input-wrapper');
        const emailInputWrapper = document.getElementById('email-input-wrapper');
        const phoneInput = document.getElementById('phone');
        const emailInput = document.getElementById('email');

        btnPhone.onclick = function() {
            btnPhone.classList.add('bg-[#d4a373]', 'text-white');
            btnPhone.classList.remove('bg-gray-200', 'text-gray-700');
            btnEmail.classList.remove('bg-[#d4a373]', 'text-white');
            btnEmail.classList.add('bg-gray-200', 'text-gray-700');
            phoneInputWrapper.classList.remove('hidden');
            emailInputWrapper.classList.add('hidden');
            phoneInput.required = true;
            emailInput.required = false;
        }
        btnEmail.onclick = function() {
            btnEmail.classList.add('bg-[#d4a373]', 'text-white');
            btnEmail.classList.remove('bg-gray-200', 'text-gray-700');
            btnPhone.classList.remove('bg-[#d4a373]', 'text-white');
            btnPhone.classList.add('bg-gray-200', 'text-gray-700');
            phoneInputWrapper.classList.add('hidden');
            emailInputWrapper.classList.remove('hidden');
            phoneInput.required = false;
            emailInput.required = true;
        }
        // Varsayılan: Telefon aktif
        btnPhone.click();

        // Telefon maskeleme
        phoneInput.addEventListener('input', function(e) {
            let raw = phoneInput.value.replace(/\D/g, '');
            if (raw.startsWith('0')) raw = raw.slice(1);
            raw = raw.slice(0, 10);
            let masked = '';
            if (raw.length > 0) masked = '(' + raw.substring(0, 3);
            if (raw.length >= 3) masked += ') ' + raw.substring(3, 6);
            if (raw.length >= 6) masked += ' ' + raw.substring(6, 8);
            if (raw.length >= 8) masked += ' ' + raw.substring(8, 10);
            phoneInput.value = masked;
        });
    </script>
</body>
</html>
