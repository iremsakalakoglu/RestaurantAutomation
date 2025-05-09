<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Mutfak Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .order-card {
            transition: all 0.3s ease;
            transform-origin: center center;
        }
        .order-card:hover {
            transform: translateY(-5px);
            z-index: 10;
        }
        .status-badge {
            transition: all 0.3s ease;
        }
        @keyframes gentle-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .new-order {
            animation: gentle-pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk
                <span class="text-gray-600 text-lg">Mutfak</span>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleView('all')" class="text-gray-600 hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-list-ul mr-2"></i>Tüm Siparişler
                </button>
                <button onclick="toggleView('waiting')" class="text-[#d4a373] hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-clock mr-2"></i>Bekleyen
                </button>
                <button onclick="toggleView('preparing')" class="text-gray-600 hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-fire mr-2"></i>Hazırlanan
                </button>
                <button onclick="toggleView('completed')" class="text-gray-600 hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-history mr-2"></i>Geçmiş Siparişler
                </button>
                <span class="text-gray-600">{{ Auth::user()->name }}</span>
                <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[#d4a373] hover:text-[#c48c63] transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="mt-24 px-8">
        <!-- Özet Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Bekleyen Siparişler</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $activeOrders->where('status', 'sipariş alındı')->count() }}</h3>
                    </div>
                    <div class="text-yellow-400">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Hazırlanan Siparişler</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $activeOrders->where('status', 'hazırlanıyor')->count() }}</h3>
                    </div>
                    <div class="text-blue-400">
                        <i class="fas fa-fire text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Bugün Tamamlanan</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $completedToday ?? 0 }}</h3>
                    </div>
                    <div class="text-green-400">
                        <i class="fas fa-check-circle text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktif Siparişler -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="ordersContainer">
            @forelse($activeOrders as $order)
                <div class="order-card bg-white rounded-lg shadow-md overflow-hidden {{ $order->status == 'sipariş alındı' ? 'new-order' : '' }}" 
                     data-status="{{ $order->status }}">
                    <!-- Sipariş Başlığı -->
                    <div class="bg-[#f5e6d3] p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-semibold">Masa {{ $order->table->table_number }}</h3>
                                    <span class="text-sm text-gray-600">Sipariş #{{ $order->id }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ $order->created_at->format('H:i') }}
                                    <span class="text-gray-500">({{ $order->created_at->diffForHumans(['parts' => 2]) }})</span>
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                @if($order->status == 'sipariş alındı') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'hazırlanıyor') bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        </div>
                    </div>
                    
                    <!-- Sipariş Detayları -->
                    <div class="p-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Sipariş İçeriği:</h4>
                        <ul class="space-y-3">
                            @foreach($order->orderDetails as $item)
                                <li class="flex justify-between items-center p-2 rounded-lg {{ !$item->is_ready ? 'bg-gray-50 border-l-4 border-yellow-400' : 'bg-green-50 border-l-4 border-green-400' }}">
                                    <div class="flex items-center space-x-3">
                                        <span class="w-7 h-7 flex items-center justify-center rounded-full {{ !$item->is_ready ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }} text-sm font-semibold">
                                            {{ $item->quantity }}
                                        </span>
                                        <div>
                                            <span class="text-gray-800 font-medium">{{ $item->product->name }}</span>
                                            @if($item->notes)
                                                <p class="text-sm text-gray-500 italic mt-1">Not: {{ $item->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($order->status == 'hazırlanıyor')
                                        <button onclick="updateItemStatus({{ $order->id }}, {{ $item->id }}, {{ $item->is_ready ? 'false' : 'true' }})" 
                                                class="text-xs px-3 py-1 rounded-full {{ !$item->is_ready ? 'bg-yellow-100 text-yellow-600 hover:bg-green-100 hover:text-green-600' : 'bg-green-100 text-green-600 hover:bg-yellow-100 hover:text-yellow-600' }} transition-colors">
                                            {{ !$item->is_ready ? 'Hazırlandı' : 'Hazır' }}
                                        </button>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full {{ !$item->is_ready ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600' }}">
                                            {{ !$item->is_ready ? 'Hazırlandı' : 'Hazır' }}
                                        </span>
                                    @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                    <!-- Aksiyon Butonları -->
                    <div class="bg-gray-50 p-4 flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            Toplam Ürün: {{ $order->orderDetails->count() }}
                            <span class="ml-2">
                                (Hazır: {{ $order->orderDetails->where('is_ready', true)->count() }}/{{ $order->orderDetails->count() }})
                            </span>
                        </span>
                        <div class="flex space-x-2">
                            @if($order->status == 'sipariş alındı')
                                <button onclick="updateOrderStatus({{ $order->id }}, 'hazırlanıyor')" 
                                        class="bg-[#d4a373] text-white px-4 py-2 rounded-lg hover:bg-[#c48c63] transition-colors flex items-center">
                                    <i class="fas fa-fire mr-2"></i>Hazırlamaya Başla
                                    </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-check-circle text-green-400 text-5xl mb-4"></i>
                        <p class="text-gray-600">Şu anda bekleyen sipariş bulunmamaktadır.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // CSRF token ayarla
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Aktif görünümü sakla
        function saveActiveView(status) {
            sessionStorage.setItem('activeView', status);
        }

        // Sipariş durumu güncelleme fonksiyonu
        function updateOrderStatus(orderId, status) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: status === 'hazırlanıyor' ? 'Siparişi hazırlamaya başlamak istediğinize emin misiniz?' : 'Siparişi hazır olarak işaretlemek istediğinize emin misiniz?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Evet',
                cancelButtonText: 'Hayır',
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/kitchen/orders/${orderId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Başarılı!',
                                text: 'Sipariş durumu güncellendi.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Bir hata oluştu.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: 'Sipariş durumu güncellenirken bir hata oluştu.'
                        });
                    });
                }
            });
        }

        // Ürün durumu güncelleme fonksiyonu
        function updateItemStatus(orderId, itemId, isReady) {
            fetch(`/kitchen/orders/${orderId}/items/${itemId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ is_ready: isReady })
            })
            .then(async response => {
                const data = await response.json().catch(e => null);
                
                if (!response.ok) {
                    throw new Error(data?.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            })
            .then(data => {
                if (data.success) {
                    if (data.allItemsReady) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Tüm Ürünler Hazır!',
                            text: 'Sipariş hazır durumuna geçirildi.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        location.reload();
                    }
                } else {
                    throw new Error(data.message || 'Bir hata oluştu.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: error.message || 'Ürün durumu güncellenirken bir hata oluştu.'
                });
            });
        }

        // Görünüm filtreleme
        function toggleView(status) {
            const orders = document.querySelectorAll('.order-card');
            orders.forEach(order => {
                const orderStatus = order.dataset.status;
                if (status === 'all') {
                    order.style.display = 'block';
                } else if (status === 'completed' && orderStatus === 'hazır') {
                    order.style.display = 'block';
                } else if (status === 'waiting' && orderStatus === 'sipariş alındı') {
                    order.style.display = 'block';
                } else if (status === 'preparing' && orderStatus === 'hazırlanıyor') {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            });

            // Butonların aktif durumunu güncelle
            document.querySelectorAll('button[onclick^="toggleView"]').forEach(btn => {
                btn.classList.remove('text-[#d4a373]');
                btn.classList.add('text-gray-600');
            });
            event.currentTarget.classList.remove('text-gray-600');
            event.currentTarget.classList.add('text-[#d4a373]');

            // Aktif görünümü kaydet
            saveActiveView(status);
        }

        // Otomatik yenileme
        let autoRefresh = true;
        const refreshInterval = 30000; // 30 saniye

        function toggleAutoRefresh() {
            autoRefresh = !autoRefresh;
            if (autoRefresh) {
                startAutoRefresh();
            }
        }

        function startAutoRefresh() {
            if (autoRefresh) {
                setTimeout(() => {
            location.reload();
                }, refreshInterval);
            }
        }

        // Sayfa yüklendiğinde son aktif görünümü geri yükle
        document.addEventListener('DOMContentLoaded', function() {
            const lastActiveView = sessionStorage.getItem('activeView');
            if (lastActiveView) {
                // Son aktif görünümü uygula
                const viewButton = document.querySelector(`button[onclick="toggleView('${lastActiveView}')"]`);
                if (viewButton) {
                    viewButton.click();
                }
            } else {
                // İlk kez yükleniyorsa bekleyen siparişleri göster
            toggleView('waiting');
        }
        });

        // Otomatik yenilemeyi başlat
        startAutoRefresh();

        // Yeni sipariş geldiğinde ses çal
        @if($activeOrders->where('status', 'sipariş alındı')->count() > 0)
            // Ses bildirimi geçici olarak devre dışı bırakıldı
            console.log('Yeni sipariş var!');
        @endif
    </script>
</body>
</html>
