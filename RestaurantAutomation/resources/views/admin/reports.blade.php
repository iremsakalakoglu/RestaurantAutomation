<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->name ?? 'Restaurant' }} - Admin Raporlama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- ApexCharts raporlama için gerekirse eklenecek --}}
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
                    <a href="{{ route('admin.reports') }}" class="flex items-center p-2 text-gray-700 {{ request()->routeIs('admin.reports*') ? 'bg-gray-100' : '' }} rounded">
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
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <h1 class="text-2xl font-bold mb-6 text-gray-800">Raporlama</h1>

        {{-- Filtreleme Formu Buraya Gelecek --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Rapor Filtreleri</h2>
            <form id="reportFilterForm" method="GET" action="{{ route('admin.reports') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="report_type" class="block text-gray-700 text-sm font-bold mb-2">Rapor Tipi:</label>
                    <select id="report_type" name="report_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Tüm Raporlar</option>
                        <option value="topSellingProducts" {{ request('report_type') == 'topSellingProducts' ? 'selected' : '' }}>En Çok Satan Ürünler</option>
                        <option value="productRevenue" {{ request('report_type') == 'productRevenue' ? 'selected' : '' }}>Ürün Bazında Gelir</option>
                        <option value="topCustomers" {{ request('report_type') == 'topCustomers' ? 'selected' : '' }}>En Çok Kazandıran Müşteriler</option>
                        <option value="leastSellingProducts" {{ request('report_type') == 'leastSellingProducts' ? 'selected' : '' }}>En Az Satan Ürünler</option>
                        <option value="waiterPerformance" {{ request('report_type') == 'waiterPerformance' ? 'selected' : '' }}>Garson Performansı</option>
                    </select>
                </div>
                <div>
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Başlangıç Tarihi:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">Bitiş Tarihi:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Filtrele
                    </button>
                     <button type="button" id="clearFilters" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Temizle
                    </button>
                </div>
            </form>
        </div>

        {{-- Rapor Sonuçları Buraya Gelecek --}}
        {{-- En Çok Satan Ürünler --}}
        <div id="topSellingProductsReport" class="bg-white rounded-lg shadow-md p-6 {{ request('report_type') == 'productRevenue' || request('report_type') == 'topCustomers' || request('report_type') == 'leastSellingProducts' || request('report_type') == 'waiterPerformance' ? 'hidden' : '' }}">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">En Çok Satan Ürünler</h2>

            @if($topSellingProducts->isEmpty())
                <p class="text-gray-600">Belirtilen tarih aralığında en çok satan ürün bulunamadı.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Adet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Gelir (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topSellingProducts as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Bilinmiyor' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->total_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ number_format($item->total_revenue, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            @if(request('report_type'))
                {{ $topSellingProducts->links() }}
            @endif
        </div>

        {{-- Ürün Bazında Gelir Raporu --}}
        <div id="productRevenueReport" class="bg-white rounded-lg shadow-md p-6 mt-6 {{ request('report_type') == 'topSellingProducts' || request('report_type') == 'topCustomers' || request('report_type') == 'leastSellingProducts' || request('report_type') == 'waiterPerformance' ? 'hidden' : '' }}">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Ürün Bazında Gelir</h2>

            @if($productRevenue->isEmpty())
                <p class="text-gray-600">Belirtilen tarih aralığında gelir getiren ürün bulunamadı.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Adet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Gelir (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($productRevenue as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Bilinmiyor' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->total_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ number_format($item->total_revenue, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            @if(request('report_type'))
                {{ $productRevenue->links() }}
            @endif
        </div>

        {{-- En Çok Kazandıran Müşteriler Raporu --}}
        <div id="topCustomersReport" class="bg-white rounded-lg shadow-md p-6 mt-6 {{ request('report_type') == 'topSellingProducts' || request('report_type') == 'productRevenue' || request('report_type') == 'leastSellingProducts' || request('report_type') == 'waiterPerformance' ? 'hidden' : '' }}">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">En Çok Kazandıran Müşteriler</h2>

            @if($topCustomers->isEmpty())
                <p class="text-gray-600">Belirtilen tarih aralığında harcama yapan müşteri bulunamadı.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri Adı</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Harcama (₺)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">En Çok Satın Alınan Ürün</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topCustomers as $customer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $customer->customer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ number_format($customer->total_spent, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->most_bought_product ?? 'Yok' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(request('report_type'))
                {{ $topCustomers->links() }}
            @endif
        </div>

        {{-- Garson Performans Raporu --}}
        <div id="waiterPerformanceReport" class="bg-white rounded-lg shadow-md p-6 mt-6 {{ request('report_type') == 'topSellingProducts' || request('report_type') == 'productRevenue' || request('report_type') == 'topCustomers' || request('report_type') == 'leastSellingProducts' ? 'hidden' : '' }}">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Garson Performansı (Gelire Göre)</h2>

            @if($waiterPerformance->isEmpty())
                <p class="text-gray-600">Belirtilen tarih aralığında performans verisi bulunamadı.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Garson Adı</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Sipariş</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Gelir (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($waiterPerformance as $waiter)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $waiter->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $waiter->total_orders }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ number_format($waiter->total_revenue, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if(request('report_type'))
                {{ $waiterPerformance->links() }}
            @endif
        </div>

        {{-- En Az Satan Ürünler Raporu --}}
        <div id="leastSellingProductsReport" class="bg-white rounded-lg shadow-md p-6 mt-6 {{ request('report_type') == 'topSellingProducts' || request('report_type') == 'productRevenue' || request('report_type') == 'topCustomers' || request('report_type') == 'waiterPerformance' ? 'hidden' : '' }}">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">En Az Satan Ürünler</h2>

            @if($leastSellingProducts->isEmpty())
                <p class="text-gray-600">Belirtilen tarih aralığında en az satan ürün bulunamadı.</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Adet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toplam Gelir (₺)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leastSellingProducts as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Bilinmiyor' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->total_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₺{{ number_format($item->total_revenue, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            @if(request('report_type'))
                {{ $leastSellingProducts->links() }}
            @endif
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

    {{-- Raporlama için özel scriptler buraya gelecek --}}
    <script>
        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('report_type').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('reportFilterForm').submit();
        });

        // Rapor tipine göre sadece ilgili div'i gösterme
        document.addEventListener('DOMContentLoaded', function() {
            const reportType = document.getElementById('report_type').value;
            const reportDivs = {
                'topSellingProducts': document.getElementById('topSellingProductsReport'),
                'productRevenue': document.getElementById('productRevenueReport'),
                'topCustomers': document.getElementById('topCustomersReport'),
                'leastSellingProducts': document.getElementById('leastSellingProductsReport'),
                'waiterPerformance': document.getElementById('waiterPerformanceReport'),
            };

            // Tüm rapor divlerini gizle
            Object.values(reportDivs).forEach(div => {
                if (div) div.classList.add('hidden');
            });

            // Seçili rapora ait divi göster veya hepsi seçiliyse hepsini göster
            if (reportType === '') {
                Object.values(reportDivs).forEach(div => {
                    if (div) div.classList.remove('hidden');
                });
            } else if (reportDivs[reportType]) {
                 // Sadece seçili rapor divini göster
                reportDivs[reportType].classList.remove('hidden');
                 // Seçili olmayan diğer rapor divlerini gizle (Tekrar gizleme işlemi)
                 Object.keys(reportDivs).forEach(key => {
                    if (key !== reportType && reportDivs[key]) {
                        reportDivs[key].classList.add('hidden');
                    }
                 });
            }
        });
    </script>

</body>
</html> 