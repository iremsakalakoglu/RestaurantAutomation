<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Kasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Özet kartlarını güncelleme fonksiyonu
        function updateSummaryCards() {
            fetch('/cashier/get-summary', {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ödeme bekleyen siparişler
                document.querySelector('.border-yellow-400 .text-2xl.font-bold').textContent = data.pendingOrders;
                // Bugün ödemesi alınanlar
                document.querySelector('.border-green-400 .text-2xl.font-bold').textContent = data.todayPayments;
                // Günlük ciro
                document.querySelector('.border-blue-400 .text-2xl.font-bold').textContent = `₺${data.dailyRevenue}`;
            });
        }

        // Global ödeme fonksiyonu
        function handlePayment(detailId) {
            // Ödeme butonunu devre dışı bırak
            const button = document.querySelector(`#order-detail-${detailId} button`);
            const quantityInput = document.querySelector(`#pay-quantity-${detailId}`);
            const payQuantity = quantityInput ? parseInt(quantityInput.value) : 1;

            if (button) {
                button.disabled = true;
                button.classList.add('opacity-50');
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/cashier/pay-detail/${detailId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    quantity: payQuantity,
                    _token: csrfToken
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text || 'Network response was not ok');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Ödeme butonunu "Ödendi" etiketi ile değiştir
                    const row = document.querySelector(`#order-detail-${detailId}`);
                    if (row) {
                        // Miktar hücresini güncelle
                        const quantityCell = row.querySelector('td:nth-child(2)');
                        if (quantityCell) {
                            quantityCell.textContent = payQuantity;
                        }
                        
                        // Durum hücresini güncelle
                        const statusCell = row.querySelector('td:last-child');
                        if (statusCell) {
                            statusCell.innerHTML = `
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-green-100 text-green-600 text-sm">
                                    Ödendi
                                </span>
                            `;
                        }
                    }

                    // Toplam tutarı güncelle
                    const totalAmount = document.querySelector('.bg-gray-50.rounded-xl .text-2xl.font-bold');
                    if (totalAmount) {
                        totalAmount.textContent = `₺${data.newTotal}`;
                    }

                    // Başarılı bildirim göster
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Ödeme başarılı!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Modalı yenile
                    const tableId = document.querySelector('[data-table-id]').getAttribute('data-table-id');
                    setTimeout(() => {
                        showAdisyonModal(tableId);
                        // Özet kartlarını güncelle
                        updateSummaryCards();
                    }, 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Hata durumunda butonu tekrar aktif et
                if (button) {
                    button.disabled = false;
                    button.classList.remove('opacity-50');
                }
                
                // Hata bildirimi göster
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Ödeme alınamadı!',
                    text: 'Lütfen tekrar deneyin.',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }

        // Ödeme miktarını güncelleme fonksiyonu
        function updatePaymentAmount(detailId, price, quantity) {
            const totalAmountElement = document.querySelector(`#total-amount-${detailId}`);
            if (totalAmountElement) {
                const total = (price * quantity).toFixed(2);
                totalAmountElement.textContent = `₺${total}`;
            }
        }

        // Tüm ürünleri ödemek için yeni fonksiyon
        function handlePaymentAll(orderId) {
            // Loading göster
            Swal.fire({
                title: 'İşleniyor...',
                html: 'Ödemeler alınıyor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Tek bir istek ile tüm ödemeleri al
            fetch(`/cashier/pay-all/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tüm ödemeler tamamlandı!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Modalı yenile
                        const tableId = document.querySelector('[data-table-id]').getAttribute('data-table-id');
                        showAdisyonModal(tableId);
                        // Özet kartlarını güncelle
                        updateSummaryCards();
                    });
                } else {
                    throw new Error(data.message || 'Bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: error.message || 'Ödemeler alınırken bir hata oluştu.',
                    confirmButtonText: 'Tamam'
                });
            });
        }
    </script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                <a href="{{ route('cashier.dashboard') }}" class="flex items-center gap-1">
                    {{ $settings->name ?? 'Restaurant' }}<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup> <span class="text-gray-600 text-lg">Kasa</span>
                </a>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative group">
                    <button class="flex items-center gap-2 text-gray-600 hover:text-[#d4a373] focus:outline-none">
                        <span>Hoş geldiniz, {{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg border z-50 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity">
                        <a href="{{ route('cashier.account.info') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Hesap Bilgilerim</a>
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
    <div class="mt-16 p-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Günlük Özet -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Ödeme Bekleyen Siparişler</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $pendingOrders }}</h3>
                    </div>
                    <div class="text-yellow-400">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Bugün Ödemesi Alınanlar</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $todayPayments }}</h3>
                    </div>
                    <div class="text-green-400">
                        <i class="fas fa-check-circle text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Günlük Ciro</p>
                        <h3 class="text-2xl font-bold text-gray-800">₺{{ number_format($todayPaymentsAmount, 2) }}</h3>
                    </div>
                    <div class="text-blue-400">
                        <i class="fas fa-money-bill-wave text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masalar Kartları Bölümü -->
        <div class="bg-white rounded-lg shadow-md mb-8 p-6">
            <h2 class="text-xl font-semibold mb-4">Masalar</h2>
            <div class="flex flex-wrap gap-6">
                @foreach($tables as $table)
                    <div class="w-64 bg-gray-50 rounded-lg shadow p-4 flex flex-col items-start">
                        <div class="text-3xl text-[#d4a373] mb-2"><i class="fa-solid fa-chair"></i></div>
                        <div class="font-bold text-lg mb-1">Masa {{ $table->table_number }}</div>
                        <div class="text-sm text-gray-600 mb-1">{{ $table->capacity }} Kişilik</div>
                        @if($table->status == 'dolu')
                            <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded mb-1">Dolu</span>
                        @else
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded mb-1">Boş</span>
                        @endif
                        <div class="text-xs text-gray-500 mb-2"><i class="fa-solid fa-user"></i> {{ $table->waiter->name ?? '-' }}</div>
                        <button class="bg-[#d4a373] text-white px-3 py-1 rounded text-sm mt-auto w-full" onclick="showAdisyonModal({{ $table->id }})">Adisyonu Görüntüle</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Adisyon Modalı -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center overflow-y-auto py-10" id="adisyonModalBg">
        <div class="relative min-h-[calc(100vh-5rem)] flex items-center justify-center w-full" id="adisyonModalContainer">
            <div id="adisyonModalContent" class="relative">
                <!-- Adisyon detayı buraya gelecek -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalBg = document.getElementById('adisyonModalBg');
            const modalContent = document.getElementById('adisyonModalContent');
            const modalContainer = document.getElementById('adisyonModalContainer');

            function showAdisyonModal(tableId) {
                fetch(`/cashier/adisyon/${tableId}`)
                    .then(res => res.text())
                    .then(html => {
                        modalContent.innerHTML = html;
                        modalBg.classList.remove('hidden');
                        modalBg.classList.add('flex');
                    });
            }
            
            function closeAdisyonModal() {
                modalBg.classList.add('hidden');
                modalBg.classList.remove('flex');
                modalContent.innerHTML = '';
            }

            // Dışarı tıklama kontrolü
            modalBg.addEventListener('click', function(event) {
                // Eğer tıklanan yer modalın kendisi veya container ise kapat
                if (event.target === modalBg || event.target === modalContainer) {
                    closeAdisyonModal();
                }
            });

            // ESC tuşu kontrolü
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !modalBg.classList.contains('hidden')) {
                    closeAdisyonModal();
                }
            });

            // Global olarak showAdisyonModal fonksiyonunu tanımla
            window.showAdisyonModal = showAdisyonModal;
            window.closeAdisyonModal = closeAdisyonModal;
        });
    </script>
</body>
</html>