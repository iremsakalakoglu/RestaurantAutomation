<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Sipariş Detayı</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                {{ $settings->name ?? 'Restaurant' }}<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup> <span class="text-gray-600 text-lg">cafe</span>
            </div>
            <!-- Hamburger menu button (mobile only) -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <!-- Menü ve kullanıcı (masaüstü) -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('menu', ['table' => $sessionTableId]) }}" class="text-gray-600 hover:text-[#d4a373] transition-colors">Menü</a>
                <span class="text-gray-600">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            </div>
        </div>
        <!-- Mobil açılır menü -->
        <div id="mobile-menu" class="md:hidden hidden flex-col gap-2 bg-[#f5e6d3] px-4 py-4 rounded-b-lg shadow-lg">
            <span class="block py-2 text-lg font-semibold text-gray-700">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            <div class="border-t border-[#e5d5c0] my-2"></div>
            <a href="{{ route('menu', ['table' => $sessionTableId]) }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Menü</a>
            <a href="{{ route('account.info') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Hesap Bilgilerim</a>
            <a href="{{ route('order.history') }}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Geçmiş Siparişlerim</a>
            <a href="{{ route('favorites')}}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Favorilerim</a>
            <a href="{{ route('notifications')}}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Bildirimlerim</a>
            <a href="{{route('support')}}" class="block py-2 text-gray-700 hover:text-[#d4a373]">Destek/Yardım</a>
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
            <a href="{{ route('order.history') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold bg-white text-[#d4a373] shadow-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Geçmiş Siparişlerim
            </a>
            <a href="{{ route('favorites')}}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-heart"></i> Favorilerim
            </a>
            <a href="{{ route('notifications')}}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-bell"></i> Bildirimlerim
            </a>
            <a href="{{route('support')}}" class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-[#f8f4f0] text-gray-700">
                <i class="fa-solid fa-circle-question"></i> Destek/Yardım
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex flex-col items-center justify-start pt-24 pb-8 px-4 min-h-[calc(100vh-4.5rem)] bg-gray-100 md:pl-64">
        <div class="w-full max-w-5xl">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('order.history') }}" class="text-[#d4a373] hover:text-[#b88b5a] transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-[#b88b5a]">Sipariş Detayı #{{ $order->id }}</h1>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <!-- Sipariş Başlığı -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Sipariş Bilgileri</h2>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($order->status === 'tamamlandı') bg-green-100 text-green-800
                                @elseif($order->status === 'iptal edildi') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->payment && $order->payment->status === 'tamamlandı')
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    Ödendi
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Sipariş Detayları -->
                    <div class="space-y-6">
                        <!-- Masa Bilgisi -->
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fa-solid fa-chair"></i>
                            <span>Masa: {{ $order->table->table_number ?? 'N/A' }}</span>
                        </div>
                        
                        <!-- Ürünler -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sipariş Edilen Ürünler</h3>
                            <div class="space-y-4">
                                @foreach($order->orderDetails as $detail)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-mug-hot text-gray-400"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-800">{{ $detail->product->name }}</h4>
                                                <p class="text-sm text-gray-500">{{ $detail->quantity }} adet</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Birim Fiyat</p>
                                            <p class="font-medium text-[#d4a373]">{{ number_format($detail->price, 2) }}₺</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Ödeme Bilgileri -->
                        @if($order->payment)
                            <div class="border-t border-gray-100 pt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ödeme Bilgileri</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Ödeme Yöntemi</p>
                                        <p class="font-medium text-gray-800">{{ ucfirst($order->payment->payment_method) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Ödeme Durumu</p>
                                        <p class="font-medium text-gray-800">{{ ucfirst($order->payment->status) }}</p>
                                    </div>
                                    @if($order->payment->paid_at)
                                        <div>
                                            <p class="text-sm text-gray-500">Ödeme Tarihi</p>
                                            <p class="font-medium text-gray-800">{{ $order->payment->paid_at->format('d.m.Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Toplam Tutar -->
                        <div class="border-t border-gray-100 pt-6">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-medium text-gray-800">Toplam Tutar</span>
                                <span class="text-2xl font-bold text-[#d4a373]">
                                    {{ number_format($order->orderDetails->sum(function($detail) {
                                        return $detail->price * $detail->quantity;
                                    }), 2) }}₺
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Hamburger menü aç/kapat
        document.getElementById('mobile-menu-btn').onclick = function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html> 