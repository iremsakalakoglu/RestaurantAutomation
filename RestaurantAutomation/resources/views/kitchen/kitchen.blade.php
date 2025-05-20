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
            <div class="flex items-center">
                <a href="#" onclick="toggleView('all', event)" class="text-gray-600 hover:text-[#d4a373] transition-colors px-4">
                    <i class="fas fa-list-ul mr-2"></i>Tüm Siparişler
                </a>
                <a href="#" onclick="toggleView('waiting', event)" class="text-gray-600 hover:text-[#d4a373] transition-colors px-4">
                    <i class="fas fa-clock mr-2"></i>Bekleyen
                </a>
                <a href="#" onclick="toggleView('preparing', event)" class="text-gray-600 hover:text-[#d4a373] transition-colors px-4">
                    <i class="fas fa-fire mr-2"></i>Hazırlanan
                </a>
                <a href="#" onclick="toggleView('completed', event)" class="text-gray-600 hover:text-[#d4a373] transition-colors px-4">
                    <i class="fas fa-history mr-2"></i>Geçmiş Siparişler
                </a>
                <div class="relative group ml-4">
                    <button class="flex items-center gap-2 text-gray-600 hover:text-[#d4a373] focus:outline-none">
                        <span>Hoş geldiniz, {{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg border z-50 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity">
                        <a href="{{ route('kitchen.account.info') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Kullanıcı Bilgilerim</a>
                        <form action="{{ route('auth.logout') }}" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Çıkış Yap</button>
                        </form>
                    </div>
                </div>
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
                <div class="order-card bg-white rounded-lg shadow-md overflow-hidden {{ $order->status == 'sipariş alındı' ? 'new-order' : '' }} flex flex-col h-full" 
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
                                @elseif($order->status == 'hazır') bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Sipariş Detayları -->
                    <div class="p-4 flex-grow">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Sipariş İçeriği:</h4>
                        <ul class="space-y-3">
                            @foreach($order->orderDetails as $item)
                                <li class="flex justify-between items-center p-2 rounded-lg
                                    @if($item->is_canceled)
                                        bg-red-50 border-l-4 border-red-400
                                    @elseif(!$item->is_ready)
                                        bg-gray-50 border-l-4 border-yellow-400
                                    @else
                                        bg-green-50 border-l-4 border-green-400
                                    @endif
                                ">
                                    <div class="flex items-center space-x-3">
                                        <span class="w-7 h-7 flex items-center justify-center rounded-full
                                            @if($item->is_canceled)
                                                bg-red-100 text-red-800
                                            @elseif(!$item->is_ready)
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-green-100 text-green-800
                                            @endif
                                            text-sm font-semibold">
                                            {{ $item->quantity }}
                                        </span>
                                        <div>
                                            <span class="text-gray-800 font-medium">{{ $item->product->name }}</span>
                                            @if($item->notes)
                                                <p class="text-sm text-gray-500 italic mt-1">Not: {{ $item->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($item->is_canceled)
                                        <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-600 border border-red-200">İptal Edildi</span>
                                    @elseif($item->is_ready)
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-600 border border-green-200">Hazırlandı</span>
                                    @elseif($order->status == 'hazırlanıyor')
                                        <div class="flex gap-2">
                                            <button onclick="updateItemStatus({{ $order->id }}, {{ $item->id }}, {{ $item->is_ready ? 'false' : 'true' }})" 
                                                    class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-yellow-600 hover:bg-green-100 hover:text-green-600 transition-colors">
                                                Hazırlandı
                                            </button>
                                            <button onclick="cancelItem({{ $order->id }}, {{ $item->id }})"
                                                    class="text-xs px-3 py-1 rounded-full bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800 transition-colors">
                                                İptal
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-600">Hazırlandı</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Aksiyon Butonları -->
                    <div class="bg-gray-50 p-4 mt-auto">
                        <div class="flex justify-between items-center">
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
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-check-circle text-green-400 text-5xl mb-4"></i>
                        <p class="text-gray-600" id="noOrdersMessage">Şu anda bekleyen sipariş bulunmamaktadır.</p>
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
            localStorage.setItem('activeView', status);
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Ürün Hazırlandı!',
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
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

        // Yeni: Ürün iptal fonksiyonu
        function cancelItem(orderId, itemId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu ürünü iptal etmek istediğinize emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, iptal et',
                cancelButtonText: 'Hayır',
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/kitchen/orders/${orderId}/items/${itemId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'İptal Edildi!',
                                text: 'Ürün başarıyla iptal edildi.',
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: error.message || 'Ürün iptal edilirken bir hata oluştu.'
                        });
                    });
                }
            });
        }

        // Görünüm filtreleme
        function toggleView(status, event) {
            console.log('toggleView çağrıldı:', status);
            const orders = document.querySelectorAll('.order-card');
            const noOrdersMessage = document.querySelector('#noOrdersMessage');
            let visibleOrders = 0;
            
            orders.forEach(order => {
                if (!order) return; // DOM'da yoksa atla
                const orderStatus = order.dataset.status;
                order.style.display = 'none'; // Önce hepsini gizle

                if (status === 'all') {
                    order.style.display = 'flex';
                    visibleOrders++;
                } else if (status === 'completed' && ['hazır', 'teslim edildi'].includes(orderStatus)) {
                    order.style.display = 'flex';
                    visibleOrders++;
                } else if (status === 'waiting' && orderStatus === 'sipariş alındı') {
                    order.style.display = 'flex';
                    visibleOrders++;
                } else if (status === 'preparing' && orderStatus === 'hazırlanıyor') {
                    order.style.display = 'flex';
                    visibleOrders++;
                }
            });

            // Durum mesajlarını güncelle
            if (noOrdersMessage) {
                if (visibleOrders === 0) {
                    noOrdersMessage.style.display = 'block';
                    switch(status) {
                        case 'all':
                            noOrdersMessage.textContent = 'Hiç sipariş bulunmamaktadır.';
                            break;
                        case 'completed':
                            noOrdersMessage.textContent = 'Tamamlanmış sipariş bulunmamaktadır.';
                            break;
                        case 'waiting':
                            noOrdersMessage.textContent = 'Bekleyen sipariş bulunmamaktadır.';
                            break;
                        case 'preparing':
                            noOrdersMessage.textContent = 'Hazırlanan sipariş bulunmamaktadır.';
                            break;
                    }
                } else {
                    noOrdersMessage.style.display = 'none';
                }
            }

            // Butonların aktif durumunu güncelle
            const allButtons = document.querySelectorAll('a[onclick^="toggleView"]');
            allButtons.forEach(btn => {
                btn.classList.remove('text-[#d4a373]');
                btn.classList.add('text-gray-600');
            });

            // Aktif butonu bul ve rengini değiştir
            let activeButton;
            if (event && event.currentTarget) {
                activeButton = event.currentTarget;
            } else {
                // Event yoksa onclick attribute'una göre butonu bul
                activeButton = document.querySelector(`a[onclick*="'${status}'"]`);
            }

            if (activeButton) {
                activeButton.classList.remove('text-gray-600');
                activeButton.classList.add('text-[#d4a373]');
            }

            // Aktif görünümü kaydet
            localStorage.setItem('activeView', status);
        }

        // Sadece window.onload ile toggleView çağrılacak, başka hiçbir yerde çağrılmayacak
        window.onload = function() {
            const lastActiveView = localStorage.getItem('activeView');
            if (lastActiveView) {
                toggleView(lastActiveView, null);
            } else {
                toggleView('all', null);
            }
        };
    </script>
</body>
</html>
