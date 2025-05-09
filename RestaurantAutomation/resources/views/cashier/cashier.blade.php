<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Kasa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk
                <span class="text-gray-600 text-lg">Kasa</span>
            </div>
            <div class="flex items-center gap-4">
                <button class="flex items-center gap-2 text-[#a86a13] font-semibold hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-clock"></i>Ödeme Bekleyen Siparişler
                </button>
                <button class="flex items-center gap-2 text-green-700 font-semibold hover:text-green-800 transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-check-circle"></i>Ödemesi Alınan Siparişler
                </button>
                <span class="text-gray-600">Hoş geldiniz, {{ Auth::user()->name }}</span>
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
                        <h3 class="text-2xl font-bold text-gray-800">{{ $pendingOrders->count() }}</h3>
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
                        <h3 class="text-2xl font-bold text-gray-800">{{ $todayPayments->count() }}</h3>
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
                        <h3 class="text-2xl font-bold text-gray-800">₺{{ number_format($todayPayments->sum('amount'), 2) }}</h3>
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
    <div class="fixed inset-0 bg-black bg-opacity-30 z-50 hidden items-center justify-center" id="adisyonModalBg">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 relative">
            <div id="adisyonModalContent">
                <!-- Adisyon detayı buraya gelecek -->
            </div>
        </div>
    </div>

    <script>
        // CSRF Token'ı al
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Global fonksiyonlar
        window.payOrderDetail = function(detailId) {
            fetch(`/cashier/pay-detail/${detailId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById(`order-detail-${detailId}`);
                    const payButton = row.querySelector('button');
                    const cells = row.querySelectorAll('td');
                    
                    // Ödeme butonunu kaldır
                    if (payButton) {
                        payButton.remove();
                    }

                    // Tüm hücrelerin metin rengini yeşil yap
                    cells.forEach((cell, index) => {
                        if (index < cells.length - 1) { // Son hücre (işlem) hariç
                            cell.classList.add('text-green-600');
                        }
                    });

                    // Ürün adının yanına (Ödendi) ekle
                    const productNameCell = cells[0];
                    const productName = productNameCell.textContent.trim();
                    productNameCell.innerHTML = `${productName} <span class="text-xs ml-2">(Ödendi)</span>`;
                    
                    // Toplam tutarı güncelle
                    const totalAmount = document.querySelector('tfoot span');
                    if (totalAmount) {
                        totalAmount.textContent = `Toplam: ₺${data.newTotal}`;
                    }

                    // Tüm ürünler ödendiyse modalı otomatik kapat
                    if (data.allPaid) {
                        closeAdisyonModal();
                        // Sayfayı yenile
                        location.reload();
                    }
                } else {
                    alert(data.message || 'Ödeme işlemi sırasında bir hata oluştu.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ödeme işlemi sırasında bir hata oluştu.');
            });
        };

        window.closeAdisyonModal = function() {
            document.getElementById('adisyonModalBg').classList.add('hidden');
            document.getElementById('adisyonModalBg').classList.remove('flex');
        };

        window.showAdisyonModal = function(tableId) {
            fetch(`/cashier/adisyon/${tableId}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('adisyonModalContent').innerHTML = html;
                    document.getElementById('adisyonModalBg').classList.remove('hidden');
                    document.getElementById('adisyonModalBg').classList.add('flex');
                });
        };

        // Sayfa yenileme
        setInterval(function() {
            location.reload();
        }, 30000); // Her 30 saniyede bir

        // Modal dışına tıklama ile kapatma
        document.getElementById('adisyonModalBg').addEventListener('click', function(e) {
            if (e.target === this) closeAdisyonModal();
        });
    </script>
</body>
</html>