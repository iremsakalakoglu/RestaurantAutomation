<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Perk - Favorilerim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold flex items-center gap-1 hover:text-[#d4a373] transition-colors">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk <span class="text-gray-600 text-lg">cafe</span>
            </a>
            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('menu') }}" class="text-gray-600 hover:text-[#d4a373] transition-colors">Menü</a>
                <span class="text-gray-600">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            </div>
        </div>
        <div id="mobile-menu" class="md:hidden hidden flex-col gap-2 bg-[#f5e6d3] px-4 py-4 rounded-b-lg shadow-lg">
            <span class="block py-2 text-lg font-semibold text-gray-700">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            <div class="border-t border-[#e5d5c0] my-2"></div>
            <a href="{{ url('/') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Ana Sayfa</a>
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
            <a href="{{ route('account.info') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-user"></i> Hesap Bilgileri
            </a>
            <a href="{{ route('order.history') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-clock-rotate-left"></i> Geçmiş Siparişlerim
            </a>
            <a href="{{ route('favorites') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold bg-white text-[#d4a373] shadow-sm">
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

    <!-- Main Content -->
    <main class="flex flex-col items-center justify-start pt-24 pb-8 px-4 min-h-[calc(100vh-4.5rem)] bg-gray-100 md:pl-64">
        <div class="w-full max-w-5xl">
            <h1 class="text-3xl font-bold text-[#b88b5a] mb-8">Favori Ürünlerim</h1>
            @if($favoriteProducts->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <i class="fa-solid fa-heart text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Favori ürün bulacak sayıda siparişiniz bulunmamaktadır.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($favoriteProducts as $product)
                        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col items-center">
                            <i class="fa-solid fa-mug-hot text-3xl text-[#d4a373] mb-3"></i>
                            <h2 class="text-xl font-semibold text-gray-800 mb-1">{{ $product->name }}</h2>
                            <p class="text-gray-500 mb-2">{{ $product->order_count }} kez sipariş edildi</p>
                            @if(isset($product->current_price))
                                <p class="text-lg font-bold text-[#b88b5a] mb-2">{{ number_format($product->current_price, 2) }}₺</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
    <script>
        document.getElementById('mobile-menu-btn').onclick = function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html> 