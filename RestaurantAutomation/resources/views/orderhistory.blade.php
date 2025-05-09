<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Perk - Sipariş Geçmişim</title>
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
            <!-- Hamburger menu button (mobile only) -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
            <!-- Menü ve kullanıcı (masaüstü) -->
            <div class="hidden md:flex items-center gap-4">
                <a href="{{ route('menu') }}" class="text-gray-600 hover:text-[#d4a373] transition-colors">Menü</a>
                <span class="text-gray-600">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name ?? 'Kullanıcı' }}</span></span>
            </div>
        </div>
        <!-- Mobil açılır menü -->
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
            <a href="{{ route('order.history') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg font-semibold bg-white text-[#d4a373] shadow-sm">
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

    <!-- Main Content -->
    <main class="flex flex-col items-center justify-start pt-24 pb-8 px-4 min-h-[calc(100vh-4.5rem)] bg-gray-100 md:pl-64">
        <div class="w-full max-w-5xl">
            <h1 class="text-3xl font-bold text-[#b88b5a] mb-8">Sipariş Geçmişim</h1>
            
            @if($orders->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <i class="fa-solid fa-clock-rotate-left text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Henüz hiç sipariş vermediniz.</p>
                    <a href="{{ route('menu') }}" class="inline-block mt-4 px-6 py-2 bg-[#d4a373] text-white rounded-lg hover:bg-[#c48c63] transition-colors">
                        Menüye Git
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-800">Sipariş #{{ $order->id }}</h2>
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
                                
                                <div class="space-y-2 mb-4">
                                    @foreach($order->orderDetails as $detail)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">{{ $detail->quantity }}x {{ $detail->product->name }}</span>
                                            <span class="font-medium">{{ number_format($detail->price * $detail->quantity, 2) }}₺</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                    <div class="text-sm text-gray-500">
                                        Masa: {{ $order->table->table_number ?? 'N/A' }}
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Toplam Tutar</div>
                                        <div class="text-lg font-semibold text-[#d4a373]">
                                            {{ number_format($order->orderDetails->sum(function($detail) {
                                                return $detail->price * $detail->quantity;
                                            }), 2) }}₺
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex justify-end gap-2">
                                    <form action="{{ route('order.repeat', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#d4a373] hover:bg-[#b88b5a] rounded transition-colors">
                                            <i class="fa-solid fa-rotate-right"></i>
                                            Siparişi Tekrarla
                                        </button>
                                    </form>
                                    <a href="{{ route('order.history.detail', $order->id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#d4a373] hover:text-[#b88b5a] transition-colors">
                                        <i class="fa-solid fa-eye"></i>
                                        Detayları Görüntüle
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
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