<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->name ?? 'Restaurant' }} - Sipariş Yönetimi</title>
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
                {{ $settings->name ?? 'Restaurant' }}<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>
                <span class="text-gray-600 text-lg">Admin</span>
            </div>
            <div class="flex items-center gap-4">
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

    <!-- Sidebar -->
    <div class="fixed left-0 top-16 h-full w-64 bg-white shadow-md">
        <div class="p-4">
            <ul class="space-y-2">
            <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-700 {{ request()->routeIs('dashboard*') ? 'bg-gray-100' : '' }} rounded">
                        <i class="fas fa-chart-line w-6"></i>
                        <span>Genel Bakış</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.products*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-utensils w-6"></i>
                        <span>Ürünler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.categories*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-list w-6"></i>
                        <span>Kategoriler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.orders*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Siparişler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tables') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.tables*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-chair w-6"></i>
                        <span>Masalar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.users*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-users w-6"></i>
                        <span>Kullanıcılar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.inventory') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.inventory*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-box w-6"></i>
                        <span>Stok Yönetimi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.support-messages') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.support-messages*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-envelope w-6"></i>
                        <span>Destek Talepleri</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.reports*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-chart-bar w-6"></i>
                        <span>Raporlama</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.settings*') ? 'bg-gray-100' : '' }}">
                        <i class="fas fa-cog w-6"></i>
                        <span>Ayarlar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 mt-16 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Sipariş Yönetimi</h1>
        </div>

        <!-- Filtreleme ve Arama -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sipariş Durumu</label>
                    <select id="filterStatus" class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        <option value="">Tüm Durumlar</option>
                        <option value="sipariş alındı">Sipariş Alındı</option>
                        <option value="hazırlanıyor">Hazırlanıyor</option>
                        <option value="hazır">Hazır</option>
                        <option value="teslim edildi">Teslim Edildi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarih Aralığı</label>
                    <div class="flex space-x-2">
                        <input type="date" id="filterStartDate" class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        <input type="date" id="filterEndDate" class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ödeme Durumu</label>
                    <select id="filterPayment" class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        <option value="">Tümü</option>
                        <option value="ödendi">Ödendi</option>
                        <option value="iptal edildi">İptal Edildi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                    <div class="relative">
                        <input type="text" id="filterSearch" placeholder="Sipariş ID veya masa no..." class="w-full border border-gray-300 rounded-md p-2 pl-10 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button id="btnApplyFilters" class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filtrele
                </button>
                <button id="btnClearFilters" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times mr-2"></i>Temizle
                </button>
            </div>
        </div>

        <!-- Siparişler -->
        <div class="bg-white overflow-hidden rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sipariş ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Masa
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarih
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Toplam
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ödeme
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">İşlemler</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->table)
                            <div class="text-sm text-gray-900">{{ $order->table->table_number }} Nolu Masa</div>
                            <div class="text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->table->status === 'boş' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $order->table->status === 'boş' ? 'Müsait' : 'Dolu' }}
                                </span>
                            </div>
                            @else
                            <div class="text-sm text-gray-500">-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->created_at ? $order->created_at->format('d.m.Y') : '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at ? $order->created_at->format('H:i') : '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                ₺{{ number_format($order->orderDetails->sum(function($detail) {
                                    return $detail->quantity * $detail->price;
                                }), 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->status == 'sipariş alındı') bg-blue-100 text-blue-800
                                @elseif($order->status == 'hazırlanıyor') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'hazır') bg-indigo-100 text-indigo-800
                                @elseif($order->status == 'teslim edildi') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                    {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($order->payment_status == 'ödendi') bg-green-100 text-green-800
                                @elseif($order->payment_status == 'iptal edildi') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="viewOrderDetails('{{ $order->id }}')" class="text-[#d4a373] hover:text-[#c48c63] mr-3">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="printOrder('{{ $order->id }}')" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-print"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Henüz sipariş bulunmamaktadır.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Sayfalama -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
            <div class="flex-1 flex justify-between sm:hidden">
                @if ($orders->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                        Önceki
                    </span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Önceki
                </a>
                @endif

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Sonraki
                </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-50 cursor-not-allowed">
                        Sonraki
                    </span>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Toplam <span class="font-medium">{{ $orders->total() }}</span> siparişten 
                        <span class="font-medium">{{ $orders->firstItem() ?? 0 }}</span> -
                        <span class="font-medium">{{ $orders->lastItem() ?? 0 }}</span> arası gösteriliyor
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <!-- Önceki Sayfa -->
                        @if ($orders->onFirstPage())
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                                <span class="sr-only">Önceki</span>
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $orders->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Önceki</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        @endif

                        <!-- Sayfa Numaraları -->
                        @php
                            $currentPage = $orders->currentPage();
                            $lastPage = $orders->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        <!-- İlk Sayfa (eğer gerekirse) -->
                        @if ($startPage > 1)
                            <a href="{{ $orders->url(1) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            1
                        </a>
                            @if ($startPage > 2)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                        @endif

                        <!-- Sayfa Aralığı -->
                        @for ($i = $startPage; $i <= $endPage; $i++)
                            @if ($i == $currentPage)
                                <span aria-current="page" class="relative inline-flex items-center px-4 py-2 border border-[#d4a373] bg-[#faf3e3] text-sm font-medium text-[#d4a373]">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $orders->url($i) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        <!-- Son Sayfa (eğer gerekirse) -->
                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            @endif
                            <a href="{{ $orders->url($lastPage) }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ $lastPage }}
                            </a>
                        @endif

                        <!-- Sonraki Sayfa -->
                        @if ($orders->hasMorePages())
                            <a href="{{ $orders->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Sonraki</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-400 cursor-not-allowed">
                                <span class="sr-only">Sonraki</span>
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Başarılı!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        </script>
    @endif

    <!-- Sipariş Detayları Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform w-full max-w-3xl">
            <div class="bg-[#e9edc9] px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="orderModalTitle">Sipariş #<span id="orderIdDisplay"></span> Detayları</h3>
                <button type="button" onclick="closeOrderModal()" class="text-gray-500 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sipariş Tarihi</p>
                        <p class="text-sm text-gray-900" id="orderDate"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Masa</p>
                        <p class="text-sm text-gray-900" id="orderTable"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sipariş Durumu</p>
                        <p class="text-sm text-gray-900" id="orderStatus"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ödeme Durumu</p>
                        <p class="text-sm text-gray-900" id="orderPayment"></p>
                    </div>
                </div>
                <div class="mb-4">
                    <h4 class="text-md font-medium text-gray-700 mb-2">Ürünler</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Adet</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Fiyat</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="orderItems">
                                <!-- Ürün listesi burada dinamik olarak doldurulacak -->
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-3 py-2 text-right text-sm font-medium text-gray-500">Toplam Tutar:</td>
                                    <td class="px-3 py-2 text-right text-sm font-medium text-gray-900" id="orderTotal"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 flex justify-end">
                <button type="button" onclick="closeOrderModal()" class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 mr-2">
                    Kapat
                </button>
                <button type="button" onclick="printOrderDetail()" class="bg-[#d4a373] text-white py-2 px-4 rounded-md hover:bg-[#c48c63]">
                    <i class="fas fa-print mr-1"></i> Yazdır
                </button>
            </div>
        </div>
    </div>

    <script>
        // Filtreleme fonksiyonları
        document.addEventListener('DOMContentLoaded', function() {
            const filterStatus = document.getElementById('filterStatus');
            const filterStartDate = document.getElementById('filterStartDate');
            const filterEndDate = document.getElementById('filterEndDate');
            const filterPayment = document.getElementById('filterPayment');
            const filterSearch = document.getElementById('filterSearch');
            const btnApplyFilters = document.getElementById('btnApplyFilters');
            const btnClearFilters = document.getElementById('btnClearFilters');
            
            // URL parametrelerini al ve form elemanlarını doldur
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('status')) filterStatus.value = urlParams.get('status');
            if (urlParams.has('start_date')) filterStartDate.value = urlParams.get('start_date');
            if (urlParams.has('end_date')) filterEndDate.value = urlParams.get('end_date');
            if (urlParams.has('payment')) filterPayment.value = urlParams.get('payment');
            if (urlParams.has('search')) filterSearch.value = urlParams.get('search');
            
            // Enter tuşuna basıldığında filtreleme yap
            filterSearch.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyFilters();
                }
            });
            
            // Filtreleri uygula
            btnApplyFilters.addEventListener('click', function() {
                applyFilters();
            });
            
            // Filtreleri temizle
            btnClearFilters.addEventListener('click', function() {
                filterStatus.value = '';
                filterStartDate.value = '';
                filterEndDate.value = '';
                filterPayment.value = '';
                filterSearch.value = '';
                
                // Tüm parametreleri temizlemek için sayfa yeniden yüklenir
                window.location.href = '{{ route('admin.orders') }}';
            });
            
            // Filtreleri uygulama fonksiyonu
            function applyFilters() {
                const status = filterStatus.value;
                const startDate = filterStartDate.value;
                const endDate = filterEndDate.value;
                const payment = filterPayment.value;
                const search = filterSearch.value;
                
                // Mevcut sayfanın URL'ini al
                let url = new URL(window.location.href);
                let searchParams = url.searchParams;
                
                // Parametreleri ekle veya güncelle
                if (status) {
                    searchParams.set('status', status);
                } else {
                    searchParams.delete('status');
                }
                
                if (startDate) {
                    searchParams.set('start_date', startDate);
                } else {
                    searchParams.delete('start_date');
                }
                
                if (endDate) {
                    searchParams.set('end_date', endDate);
                } else {
                    searchParams.delete('end_date');
                }
                
                if (payment) {
                    searchParams.set('payment', payment);
                } else {
                    searchParams.delete('payment');
                }
                
                if (search) {
                    searchParams.set('search', search);
                } else {
                    searchParams.delete('search');
                }
                
                // Her zaman ilk sayfaya dön
                searchParams.set('page', 1);
                
                // URL'i güncelle ve sayfayı yeniden yükle
                window.location.href = url.toString();
            }
        });
        
        function viewOrderDetails(orderId) {
            fetch(`/api/orders/${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Sipariş detayları alınamadı');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Sipariş detayları:', data); // Hata ayıklama için

                    document.getElementById('orderIdDisplay').textContent = data.id;
                    document.getElementById('orderDate').textContent = 
                        data.created_at 
                            ? new Date(data.created_at).toLocaleDateString('tr-TR') + ' ' + 
                              new Date(data.created_at).toLocaleTimeString('tr-TR')
                            : '-';
                    
                    document.getElementById('orderTable').textContent = 
                        data.table ? `${data.table.table_number} Nolu Masa` : '-';
                    
                    let statusText = '';
                    let statusClass = '';
                    switch(data.status) {
                        case 'sipariş alındı': 
                            statusText = 'Sipariş Alındı'; 
                            statusClass = 'bg-blue-100 text-blue-800';
                            break;
                        case 'hazırlanıyor': 
                            statusText = 'Hazırlanıyor'; 
                            statusClass = 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'hazır': 
                            statusText = 'Hazır'; 
                            statusClass = 'bg-indigo-100 text-indigo-800';
                            break;
                        case 'teslim edildi': 
                            statusText = 'Teslim Edildi'; 
                            statusClass = 'bg-green-100 text-green-800';
                            break;
                        default: 
                            statusText = data.status;
                            statusClass = 'bg-gray-100 text-gray-800';
                    }
                    
                    // Durumu görsel olarak göster
                    document.getElementById('orderStatus').innerHTML = `
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${statusText}
                        </span>
                    `;
                    
                    // Ödeme durumunu göster
                    const paymentStatus = data.payment_status === 'ödendi' ? 'Ödendi' : 'İptal Edildi';
                    const paymentClass = data.payment_status === 'ödendi' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    document.getElementById('orderPayment').innerHTML = `
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${paymentClass}">
                            ${paymentStatus}
                        </span>
                    `;
                    
                    // Sipariş ürünlerini listele
                    const orderItems = document.getElementById('orderItems');
                    orderItems.innerHTML = '';
                    
                    let total = 0;
                    if (data.order_details && Array.isArray(data.order_details)) {
                        data.order_details.forEach(item => {
                            // Fiyat ve miktarı sayıya dönüştür
                            const price = parseFloat(item.price) || 0;
                            const quantity = parseInt(item.quantity) || 0;
                            const itemTotal = quantity * price;
                            total += itemTotal;
                            
                            // Ürün adını kontrol et
                            const productName = item.product_name || (item.product ? item.product.name : `Ürün #${item.product_id}`);
                            
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${productName}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-center">
                                    <div class="text-sm text-gray-900">${quantity}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">₺${price.toFixed(2)}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900">₺${itemTotal.toFixed(2)}</div>
                                </td>
                            `;
                            orderItems.appendChild(row);
                        });
                    } else {
                        console.error('Sipariş detayları bulunamadı veya yanlış formatta', data);
                        orderItems.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500 py-4">Sipariş detayları bulunamadı</td></tr>';
                    }
                    
                    document.getElementById('orderTotal').textContent = `₺${total.toFixed(2)}`;
                    document.getElementById('orderDetailsModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Sipariş detayları alınırken hata oluştu:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Sipariş detayları alınırken bir hata oluştu: ' + error.message,
                    });
                });
        }
        
        function closeOrderModal() {
            document.getElementById('orderDetailsModal').classList.add('hidden');
        }

        // Siparişi yazdırma
        function printOrder(orderId) {
            // Önce sipariş detaylarını al, sonra yazdır
            fetch(`/api/orders/${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Sipariş detayları alınamadı');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Yazdırılacak sipariş detayları:', data); // Hata ayıklama için
                    
                    // Sipariş tarihi formatı
                    let orderDate = '-';
                    try {
                        if (data.created_at) {
                            orderDate = new Date(data.created_at).toLocaleDateString('tr-TR') + ' ' + 
                                        new Date(data.created_at).toLocaleTimeString('tr-TR');
                        }
                    } catch (e) {
                        console.error('Tarih formatı hatası:', e);
                    }
                    
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                        <head>
                            <title>Sipariş #${data.id}</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                .header { text-align: center; margin-bottom: 20px; }
                                .order-info { margin-bottom: 20px; }
                                .order-info p { margin: 5px 0; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                th { background-color: #f2f2f2; }
                                .total { font-weight: bold; text-align: right; margin-top: 10px; }
                                .footer { margin-top: 30px; text-align: center; font-size: 12px; }
                                @media print {
                                    button { display: none; }
                                }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h2>Sipariş Fişi</h2>
                            </div>
                            <div class="order-info">
                                <p><strong>Sipariş No:</strong> #${data.id}</p>
                                <p><strong>Tarih:</strong> ${orderDate}</p>
                                <p><strong>Masa:</strong> ${data.table ? data.table.table_number + ' Nolu Masa' : '-'}</p>
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Adet</th>
                                        <th>Fiyat</th>
                                        <th>Toplam</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `);
                    
                    let total = 0;
                    if (data.order_details && Array.isArray(data.order_details)) {
                        data.order_details.forEach(item => {
                            // Fiyat ve miktarı sayıya dönüştür
                            const price = parseFloat(item.price) || 0;
                            const quantity = parseInt(item.quantity) || 0;
                            const itemTotal = quantity * price;
                            total += itemTotal;
                            
                            // Ürün adını kontrol et
                            const productName = item.product_name || (item.product ? item.product.name : `Ürün #${item.product_id}`);
                            
                            printWindow.document.write(`
                                <tr>
                                    <td>${productName}</td>
                                    <td>${quantity}</td>
                                    <td>₺${price.toFixed(2)}</td>
                                    <td>₺${itemTotal.toFixed(2)}</td>
                                </tr>
                            `);
                        });
                    } else {
                        printWindow.document.write(`
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 20px;">
                                    Sipariş detayları bulunamadı
                                </td>
                            </tr>
                        `);
                    }
                    
                    printWindow.document.write(`
                                </tbody>
                            </table>
                            <div class="total">Toplam Tutar: ₺${total.toFixed(2)}</div>
                            <div class="footer">
                                <p>Bizi tercih ettiğiniz için teşekkür ederiz!</p>
                            </div>
                            <button onclick="window.print();" style="margin-top: 20px; padding: 10px 20px;">Yazdır</button>
                        </body>
                        </html>
                    `);
                    
                    printWindow.document.close();
                    printWindow.focus();
                })
                .catch(error => {
                    console.error('Sipariş yazdırma hatası:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Sipariş yazdırılırken bir hata oluştu: ' + error.message,
                    });
                });
        }
        
        // Modal içindeki yazdırma fonksiyonu
        function printOrderDetail() {
            const orderId = document.getElementById('orderIdDisplay').textContent;
            printOrder(orderId);
        }
    </script>
</body>
</html> 