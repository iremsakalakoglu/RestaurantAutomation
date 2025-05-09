<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Stok Yönetimi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .custom-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .custom-select option {
            padding: 0.5rem;
            font-size: 0.875rem;
        }

        .custom-select option:hover {
            background-color: #f3f4f6;
        }

        /* Firefox için özel stil */
        @-moz-document url-prefix() {
            .custom-select {
                scrollbar-width: thin;
                scrollbar-color: #d4a373 #f3f4f6;
            }
        }

        /* Webkit (Chrome, Safari) için özel stil */
        .custom-select::-webkit-scrollbar {
            width: 8px;
        }

        .custom-select::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }

        .custom-select::-webkit-scrollbar-thumb {
            background: #d4a373;
            border-radius: 4px;
        }

        .custom-select::-webkit-scrollbar-thumb:hover {
            background: #c48c63;
        }

        /* Kategori dropdown'u için özel stil */
        .custom-select-category {
            max-height: 44px;
            overflow-y: auto;
        }
        .custom-select-category option {
            height: 44px;
        }
        .custom-select-category:focus {
            outline: none;
        }
        /* Açılırken 4 seçenek kadar yükseklik ve scroll */
        .custom-select-category[multiple], .custom-select-category[size] {
            max-height: 176px;
            overflow-y: auto;
        }
        /* Webkit (Chrome, Safari) için özel stil */
        .custom-select-category::-webkit-scrollbar {
            width: 8px;
        }
        .custom-select-category::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }
        .custom-select-category::-webkit-scrollbar-thumb {
            background: #d4a373;
            border-radius: 4px;
        }
        .custom-select-category::-webkit-scrollbar-thumb:hover {
            background: #c48c63;
        }
        /* Kategori custom dropdown için özel stil */
        #filter-category-results {
            max-height: 176px; /* 4x44px */
            overflow-y: auto;
        }
        #filter-category-results div {
            padding: 10px 16px;
            cursor: pointer;
        }
        #filter-category-results div:hover {
            background: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk
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
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-chart-line w-6"></i>
                        <span>Genel Bakış</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-utensils w-6"></i>
                        <span>Ürünler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-list w-6"></i>
                        <span>Kategoriler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Siparişler</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.tables') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-chair w-6"></i>
                        <span>Masalar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-users w-6"></i>
                        <span>Kullanıcılar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.inventory') }}" class="flex items-center p-2 text-gray-700 bg-gray-100 rounded">
                        <i class="fas fa-box w-6"></i>
                        <span>Stok Yönetimi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
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
            <h1 class="text-2xl font-bold">Stok Yönetimi</h1>
            <div class="flex items-center gap-4">
                <button onclick="openSuppliersModal()" 
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                    <i class="fas fa-industry mr-2"></i>Tedarikçileri Yönet
                </button>
                <button onclick="openAddStockModal()" 
                        class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                    <i class="fas fa-plus mr-2"></i>Yeni Stok Ekle
                </button>
            </div>
        </div>

        <!-- Kritik Stok Uyarıları -->
        @if($lowStockProducts->isNotEmpty())
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-4 text-red-600">Kritik Stok Uyarıları</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($lowStockProducts as $stock)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-red-700">{{ $stock->product->name }}</h3>
                            <p class="text-sm text-red-600">
                                Mevcut Stok: {{ $stock->quantity }} {{ $stock->unit }}
                            </p>
                        </div>
                        <button onclick="openStockModal({{ $stock->id }}, '{{ $stock->product->name }}')" 
                                class="text-red-600 hover:text-red-800">
                            <i class="fas fa-plus-circle text-xl"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Filtreleme Bölümü -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Stok Hareketleri Filtrele</h2>
                <button id="advanced-filters-toggle" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-sliders-h mr-1"></i>Gelişmiş Filtreler
                </button>
            </div>

            <!-- Temel Filtreler -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Ürün Filtresi -->
                <div class="relative">
                    <input type="text" id="filter-product-search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Ürün ara...">
                    <input type="hidden" id="filter-product">
                    <div id="filter-product-results" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-60 overflow-y-auto hidden"></div>
                </div>

                <!-- Kategori Filtresi -->
                <div class="relative">
                    <input type="text" id="filter-category-search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Kategori ara...">
                    <input type="hidden" id="filter-category">
                    <div id="filter-category-results" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-44 overflow-y-auto hidden" style="max-height: 176px;"></div>
                </div>

                <!-- Hareket Tipi Filtresi -->
                <div>
                    <select id="filter-type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm">
                        <option value="">Tüm Hareketler</option>
                        <option value="giris">Giriş</option>
                        <option value="cikis">Çıkış</option>
                    </select>
                </div>

                <!-- İşlem Tarihi Aralığı -->
                <div class="relative">
                    <input type="text" id="date-range" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="İşlem Tarihi Aralığı">
                    <input type="hidden" id="filter-date-from">
                    <input type="hidden" id="filter-date-to">
                </div>
            </div>

            <!-- Gelişmiş Filtreler (Başlangıçta Gizli) -->
            <div id="advanced-filters" class="hidden border-t pt-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Tedarikçi ve Üretici -->
                    <div class="space-y-4">
                        <input type="text" id="filter-supplier" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Tedarikçi">
                        <input type="text" id="filter-manufacturer" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Üretici">
                        <!-- Geliş Tarihi Aralığı -->
                        <div class="relative">
                            <input type="text" id="arrival-date-range" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Geliş Tarihi Aralığı">
                            <input type="hidden" id="filter-arrival-date-from">
                            <input type="hidden" id="filter-arrival-date-to">
                        </div>
                    </div>

                    <!-- Miktar ve Fiyat Aralıkları -->
                    <div class="space-y-4">
                        <div class="flex space-x-2">
                            <input type="number" id="filter-min-quantity" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Min Miktar">
                            <input type="number" id="filter-max-quantity" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Max Miktar">
                        </div>
                        <div class="flex space-x-2">
                            <input type="number" id="filter-min-purchase-price" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Min Alış">
                            <input type="number" id="filter-max-purchase-price" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Max Alış">
                        </div>
                        <div class="flex space-x-2">
                            <input type="number" id="filter-min-sale-price" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Min Satış">
                            <input type="number" id="filter-max-sale-price" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm" placeholder="Max Satış">
                        </div>
                    </div>

                    <!-- Sıralama -->
                    <div class="space-y-4">
                        <select id="filter-sort-by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm">
                            <option value="created_at">Tarihe Göre Sırala</option>
                            <option value="quantity">Miktara Göre Sırala</option>
                            <option value="purchase_price">Alış Fiyatına Göre Sırala</option>
                            <option value="sale_price">Satış Fiyatına Göre Sırala</option>
                        </select>
                        <select id="filter-sort-direction" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373] transition-all text-sm">
                            <option value="desc">Azalan</option>
                            <option value="asc">Artan</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Filtreleme Butonları -->
            <div class="flex justify-end space-x-2 mt-4">
                <button id="filter-clear" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm">
                    <i class="fas fa-times mr-1"></i>Temizle
                </button>
                <button id="filter-button" class="px-4 py-2 bg-[#d4a373] text-white rounded-md hover:bg-[#c48c63] transition-colors text-sm">
                    <i class="fas fa-filter mr-1"></i>Filtrele
                </button>
            </div>
        </div>

        <!-- Stok Hareketleri Tablosu -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-lg font-semibold">Stok Hareketleri</h2>
                <div class="flex gap-2">
                    <button id="view-products" class="text-gray-600 hover:text-gray-800 px-3 py-1 rounded border border-gray-300 bg-white transition-all">
                        <i class="fas fa-th-list mr-1"></i>Ürün Listesi
                    </button>
                    <button id="view-movements" class="text-white bg-[#d4a373] px-3 py-1 rounded border border-[#d4a373] transition-all">
                        <i class="fas fa-history mr-1"></i>Stok Hareketleri
                    </button>
                </div>
            </div>
            <div id="stock-movements-container">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih / Saat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Geliş Tarihi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hareket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Birim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alış Fiyatı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satış Fiyatı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody id="stock-movements-body" class="bg-white divide-y divide-gray-200">
                        @foreach($tableMovements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->arrival_date ? $movement->arrival_date->format('d.m.Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $movement->stock->product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $movement->stock->product->category->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold {{ $movement->type == 'giris' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $movement->type == 'giris' ? 'Giriş' : 'Çıkış' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $movement->type == 'giris' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->type == 'giris' ? '+' : '-' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->stock->unit }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->purchase_price ? number_format($movement->purchase_price, 2) . ' ₺' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->sale_price ? number_format($movement->sale_price, 2) . ' ₺' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $movement->description }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- Sayfalama -->
                <div class="px-6 py-4 bg-white border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            Toplam {{ $tableMovements->total() }} kayıt içerisinden 
                            {{ $tableMovements->firstItem() }}-{{ $tableMovements->lastItem() }} 
                            arası gösteriliyor
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($tableMovements->onFirstPage())
                                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $tableMovements->previousPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            @foreach ($tableMovements->getUrlRange(1, $tableMovements->lastPage()) as $page => $url)
                                @if ($page == $tableMovements->currentPage())
                                    <span class="px-3 py-1 bg-[#d4a373] text-white rounded-md">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($tableMovements->hasMorePages())
                                <a href="{{ $tableMovements->nextPageUrl() }}" class="px-3 py-1 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ürün Listesi (Başlangıçta gizli) -->
            <div id="products-container" class="hidden">
                <div class="p-4 flex justify-between items-center">
                    <div class="flex space-x-4">
                        <div class="relative w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input id="searchInput" type="text" placeholder="Ürün ara..." class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        </div>
                    </div>
                </div>
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün Adı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tedarikçi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Birim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miktar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alış Fiyatı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satış Fiyatı</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 product-row" data-name="{{ strtolower($product->name) }}" data-barcode="{{ $product->barcode ?? '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->category->name }}</div>
                        </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 flex items-center gap-2">
                                    @if($product->stock && $product->stock->supplier)
                                        {{ $product->stock->supplier }}
                                    @elseif($product->stock)
                                        <span class="text-gray-400">-</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($product->stock)
                                    {{ $product->stock->unit }}
                                @else
                                        <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->stock)
                                <span class="text-sm font-medium {{ $product->stock->quantity <= 5 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $product->stock->quantity }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">Stok girilmemiş</span>
                            @endif
                        </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->stock && $product->stock->purchase_price)
                                        {{ number_format($product->stock->purchase_price, 2) }} ₺
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($product->stock && $product->stock->sale_price)
                                        {{ number_format($product->stock->sale_price, 2) }} ₺
                            @else
                                        <span class="text-gray-400">-</span>
                            @endif
                                </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($product->stock)
                                <button onclick="editSupplierForStock({{ $product->stock->id }}, '{{ $product->stock->supplier }}')" class="text-blue-500 hover:text-blue-700 mr-2" title="Tedarikçi Düzenle">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button onclick="showStockMovements({{ $product->stock->id }}, '{{ $product->name }}')" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-history"></i>
                                </button>
                            @else
                                <span class="text-gray-400 text-xs">Stok girilmemiş</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Henüz ürün bulunmamaktadır.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <!-- Son 7 Günlük Stok Hareketleri Grafiği -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100 mt-6 p-6">
            <h2 class="text-lg font-semibold mb-4">Son 7 Günlük Stok Hareketleri (Miktar Bazlı)</h2>
            <div style="height: 300px;">
                <canvas id="stockMovementsChart"></canvas>
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

    <script>
        // Stok Hareketleri ve Ürün Listesi arasında geçiş
        const viewProductsBtn = document.getElementById('view-products');
        const viewMovementsBtn = document.getElementById('view-movements');
        const productsContainer = document.getElementById('products-container');
        const stockMovementsContainer = document.getElementById('stock-movements-container');

        viewProductsBtn.addEventListener('click', function() {
            productsContainer.classList.remove('hidden');
            stockMovementsContainer.classList.add('hidden');
            
            viewProductsBtn.className = 'text-white bg-[#d4a373] px-3 py-1 rounded border border-[#d4a373] transition-all';
            viewMovementsBtn.className = 'text-gray-600 hover:text-gray-800 px-3 py-1 rounded border border-gray-300 bg-white transition-all';
        });

        viewMovementsBtn.addEventListener('click', function() {
            productsContainer.classList.add('hidden');
            stockMovementsContainer.classList.remove('hidden');
            
            viewProductsBtn.className = 'text-gray-600 hover:text-gray-800 px-3 py-1 rounded border border-gray-300 bg-white transition-all';
            viewMovementsBtn.className = 'text-white bg-[#d4a373] px-3 py-1 rounded border border-[#d4a373] transition-all';
        });

        // Filtreleme
        document.addEventListener('DOMContentLoaded', function() {
            const filterButton = document.getElementById('filter-button');
            const filterClear = document.getElementById('filter-clear');
            const advancedFiltersToggle = document.getElementById('advanced-filters-toggle');
            const advancedFilters = document.getElementById('advanced-filters');

            // Gelişmiş filtreler toggle
            if (advancedFiltersToggle) {
                advancedFiltersToggle.addEventListener('click', function() {
                    advancedFilters.classList.toggle('hidden');
                    this.innerHTML = advancedFilters.classList.contains('hidden')
                        ? '<i class="fas fa-sliders-h mr-1"></i>Gelişmiş Filtreler'
                        : '<i class="fas fa-times mr-1"></i>Gelişmiş Filtreleri Gizle';
                });
            }

            // Flatpickr tarih seçici
            flatpickr("#date-range", {
                mode: "range",
                dateFormat: "d.m.Y",
                locale: "tr",
                placeholder: "İşlem Tarihi Aralığı Seçin",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        document.getElementById('filter-date-from').value = selectedDates[0].toISOString().split('T')[0];
                        document.getElementById('filter-date-to').value = selectedDates[1].toISOString().split('T')[0];
                    }
                }
            });

            // Geliş tarihi için flatpickr
            flatpickr("#arrival-date-range", {
                mode: "range",
                dateFormat: "d.m.Y",
                locale: "tr",
                placeholder: "Geliş Tarihi Aralığı Seçin",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length === 2) {
                        document.getElementById('filter-arrival-date-from').value = selectedDates[0].toISOString().split('T')[0];
                        document.getElementById('filter-arrival-date-to').value = selectedDates[1].toISOString().split('T')[0];
                    }
                }
            });

            if(filterButton) {
                filterButton.addEventListener('click', function() {
                    loadStockMovements();
                });
            }

            if(filterClear) {
                filterClear.addEventListener('click', function() {
                    // Tüm form elemanlarını seç
                    document.getElementById('filter-product-search').value = '';
                    document.getElementById('filter-category-search').value = '';
                    document.getElementById('filter-type').value = '';
                    document.getElementById('filter-supplier').value = '';
                    document.getElementById('filter-manufacturer').value = '';
                    document.getElementById('filter-min-quantity').value = '';
                    document.getElementById('filter-max-quantity').value = '';
                    document.getElementById('filter-min-purchase-price').value = '';
                    document.getElementById('filter-max-purchase-price').value = '';
                    document.getElementById('filter-min-sale-price').value = '';
                    document.getElementById('filter-max-sale-price').value = '';
                    document.getElementById('date-range').value = '';
                    document.getElementById('filter-date-from').value = '';
                    document.getElementById('filter-date-to').value = '';
                    document.getElementById('arrival-date-range').value = '';
                    document.getElementById('filter-arrival-date-from').value = '';
                    document.getElementById('filter-arrival-date-to').value = '';
                    document.getElementById('filter-sort-by').value = 'created_at';
                    document.getElementById('filter-sort-direction').value = 'desc';

                    // Filtreleri uygula
                    loadStockMovements();
                });
            }

            // Ürün arama autocomplete
            const productSearchInput = document.getElementById('filter-product-search');
            const productResultsDiv = document.getElementById('filter-product-results');
            const productIdInput = document.getElementById('filter-product');
            let allProducts = [];
            try {
                allProducts = @json($products);
            } catch (e) {
                allProducts = [];
            }
            if (productSearchInput) {
                productSearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    if (searchTerm.length < 2) {
                        productResultsDiv.classList.add('hidden');
                        return;
                    }
                    const filtered = allProducts.filter(p => p.name.toLowerCase().includes(searchTerm));
                    if (filtered.length > 0) {
                        productResultsDiv.innerHTML = '';
                        filtered.forEach(product => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                            div.textContent = product.name;
                            div.addEventListener('click', function() {
                                productSearchInput.value = product.name;
                                productIdInput.value = product.id;
                                productResultsDiv.classList.add('hidden');
                            });
                            productResultsDiv.appendChild(div);
                        });
                        productResultsDiv.classList.remove('hidden');
                    } else {
                        productResultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">Sonuç bulunamadı</div>';
                        productResultsDiv.classList.remove('hidden');
                    }
                });
                document.addEventListener('click', function(e) {
                    if (!productSearchInput.contains(e.target) && !productResultsDiv.contains(e.target)) {
                        productResultsDiv.classList.add('hidden');
                    }
                });
            }

            // Kategori arama autocomplete
            const categorySearchInput = document.getElementById('filter-category-search');
            const categoryResultsDiv = document.getElementById('filter-category-results');
            const categoryIdInput = document.getElementById('filter-category');
            let allCategories = [];
            try {
                allCategories = @json($categories);
            } catch (e) {
                allCategories = [];
            }
            if (categorySearchInput) {
                categorySearchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    if (searchTerm.length < 1) {
                        categoryResultsDiv.classList.add('hidden');
                        return;
                    }
                    const filtered = allCategories.filter(c => c.name.toLowerCase().includes(searchTerm));
                    if (filtered.length > 0) {
                        categoryResultsDiv.innerHTML = '';
                        filtered.forEach(category => {
                            const div = document.createElement('div');
                            div.textContent = category.name;
                            div.addEventListener('click', function() {
                                categorySearchInput.value = category.name;
                                categoryIdInput.value = category.id;
                                categoryResultsDiv.classList.add('hidden');
                            });
                            categoryResultsDiv.appendChild(div);
                        });
                        categoryResultsDiv.classList.remove('hidden');
                    } else {
                        categoryResultsDiv.innerHTML = '<div class="px-4 py-2 text-gray-500">Sonuç bulunamadı</div>';
                        categoryResultsDiv.classList.remove('hidden');
                    }
                });
                document.addEventListener('click', function(e) {
                    if (!categorySearchInput.contains(e.target) && !categoryResultsDiv.contains(e.target)) {
                        categoryResultsDiv.classList.add('hidden');
                    }
                });
            }
        });

        function loadStockMovements() {
            const filterProduct = document.getElementById('filter-product');
            const filterCategory = document.getElementById('filter-category');
            const filterType = document.getElementById('filter-type');
            const filterSupplier = document.getElementById('filter-supplier');
            const filterManufacturer = document.getElementById('filter-manufacturer');
            const filterMinQuantity = document.getElementById('filter-min-quantity');
            const filterMaxQuantity = document.getElementById('filter-max-quantity');
            const filterMinPurchasePrice = document.getElementById('filter-min-purchase-price');
            const filterMaxPurchasePrice = document.getElementById('filter-max-purchase-price');
            const filterMinSalePrice = document.getElementById('filter-min-sale-price');
            const filterMaxSalePrice = document.getElementById('filter-max-sale-price');
            const filterDateFrom = document.getElementById('filter-date-from');
            const filterDateTo = document.getElementById('filter-date-to');
            const filterArrivalDateFrom = document.getElementById('filter-arrival-date-from');
            const filterArrivalDateTo = document.getElementById('filter-arrival-date-to');
            const filterSortBy = document.getElementById('filter-sort-by');
            const filterSortDirection = document.getElementById('filter-sort-direction');
            
            // Loading göster
            Swal.fire({
                title: 'Yükleniyor...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // URL parametrelerini oluştur
            const params = new URLSearchParams();
            if (filterProduct.value) params.append('product_id', filterProduct.value);
            if (filterCategory.value) params.append('category_id', filterCategory.value);
            if (filterType.value) params.append('type', filterType.value);
            if (filterSupplier.value) params.append('supplier', filterSupplier.value);
            if (filterManufacturer.value) params.append('manufacturer', filterManufacturer.value);
            if (filterMinQuantity.value) params.append('min_quantity', filterMinQuantity.value);
            if (filterMaxQuantity.value) params.append('max_quantity', filterMaxQuantity.value);
            if (filterMinPurchasePrice.value) params.append('min_purchase_price', filterMinPurchasePrice.value);
            if (filterMaxPurchasePrice.value) params.append('max_purchase_price', filterMaxPurchasePrice.value);
            if (filterMinSalePrice.value) params.append('min_sale_price', filterMinSalePrice.value);
            if (filterMaxSalePrice.value) params.append('max_sale_price', filterMaxSalePrice.value);
            if (filterDateFrom.value) params.append('date_from', filterDateFrom.value);
            if (filterDateTo.value) params.append('date_to', filterDateTo.value);
            if (filterArrivalDateFrom.value) params.append('arrival_date_from', filterArrivalDateFrom.value);
            if (filterArrivalDateTo.value) params.append('arrival_date_to', filterArrivalDateTo.value);
            if (filterSortBy.value) params.append('sort_by', filterSortBy.value);
            if (filterSortDirection.value) params.append('sort_direction', filterSortDirection.value);

            // CSRF token'ı al
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/admin/inventory/movements?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                
                if (data.success) {
                    const movementsBody = document.getElementById('stock-movements-body');
                    movementsBody.innerHTML = '';
                    
                    if (!data.movements.data || data.movements.data.length === 0) {
                        movementsBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Filtreleme kriterlerine uygun stok hareketi bulunamadı.
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    data.movements.data.forEach(movement => {
                        if (!movement || !movement.stock || !movement.stock.product) {
                            console.warn('Geçersiz hareket verisi:', movement);
                            return;
                        }

                        const row = document.createElement('tr');
                        row.className = 'hover:bg-gray-50';
                        
                        const date = new Date(movement.created_at);
                        const formattedDate = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getFullYear()} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${formattedDate}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${movement.arrival_date ? new Date(movement.arrival_date).toLocaleString('tr-TR') : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">${movement.stock.product.name}</div>
                                <div class="text-xs text-gray-500">${movement.stock.product.category.name}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${movement.type === 'giris' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${movement.type === 'giris' ? 'Giriş' : 'Çıkış'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ${movement.type === 'giris' ? 'text-green-600' : 'text-red-600'}">
                                ${movement.type === 'giris' ? '+' : '-'}${movement.quantity}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${movement.stock.unit}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${movement.purchase_price ? parseFloat(movement.purchase_price).toFixed(2) + ' ₺' : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${movement.sale_price ? parseFloat(movement.sale_price).toFixed(2) + ' ₺' : '-'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${movement.description || '-'}
                            </td>
                        `;
                        
                        movementsBody.appendChild(row);
                    });
                } else {
                    Swal.fire('Hata!', data.message || 'Stok hareketleri yüklenirken bir hata oluştu', 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Hata:', error);
                Swal.fire('Hata!', 'Stok hareketleri yüklenirken bir hata oluştu: ' + error.message, 'error');
            });
        }

        // Ürün arama işlevi
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            const productRows = document.querySelectorAll('.product-row');

            searchInput.addEventListener('input', () => {
                const searchTerm = searchInput.value.toLowerCase();
                productRows.forEach(row => {
                    const name = row.dataset.name;
                    const barcode = row.dataset.barcode || '';
                    row.style.display = (name.includes(searchTerm) || barcode.includes(searchTerm)) ? '' : 'none';
                });
            });
        }

        // Yeni stok ekle modalı
        function openAddStockModal(savedValues = null) {
            // Tüm ürünleri al
            fetch('/admin/inventory/products', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ürünler yüklenirken bir hata oluştu');
                }
                return response.json();
            })
                .then(data => {
                    if (data.success) {
                    const products = data.products;

                        Swal.fire({
                        title: 'Stok İşlemi',
                            html: `
                                <form id="addStockForm" class="text-left">
                                    <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Ara</label>
                                    <div class="flex space-x-2">
                                        <div class="relative w-full">
                                            <input type="text" id="productSearch" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Ürün adını yazın...">
                                            <input type="hidden" id="selectedProductId">
                                            <div id="searchResults" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-60 overflow-y-auto hidden"></div>
                                        </div>
                                        <div class="relative w-64">
                                            <input type="text" id="modalBarcodeInput" class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md" placeholder="Barkod ile ara...">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                                <i class="fas fa-barcode text-gray-400"></i>
                                            </span>
                                            <div id="barcodeSearchResults" class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-300 max-h-60 overflow-y-auto hidden"></div>
                                        </div>
                                    </div>
                                    <div id="selectedProductInfo" class="mt-2 text-sm text-gray-500 hidden">
                                        <span>Seçilen ürün: <strong id="selectedProductName"></strong></span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                    <div class="mb-4">
                                     <label class="block text-sm font-medium text-gray-700 mb-1">İşlem Tipi</label>
                                         <select id="stockType" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            <option value="giris">Stok Ekle</option>
                                            <option value="cikis">Stok Çıkar</option>
                                         </select>
                                    </div>

                                    <div class="mb-4">
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Birim</label>
                                         <select id="stockUnit" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            <option value="adet">Adet</option>
                                            option value="kg">Kilogram</option>
                                            <option value="lt">Litre</option>
                                            <option value="gr">Gram</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Miktar</label>
                                    <input type="number" id="stockQuantity" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" value="1">
                                    </div>

                                    <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <input type="text" id="stockDescription" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Opsiyonel">
                                    </div>
                             </div>

                            <div>
                                    <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tedarikçi</label>
                                    <div class="flex items-center space-x-2">
                                        <select id="stockSupplier" class="flex-1 px-3 py-2 border border-gray-300 rounded-md max-h-48 overflow-y-auto custom-select">
                                            <option value="">Tedarikçi Seçin</option>
                                        </select>
                                    </div>
                            </div>

                                    <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alış Fiyatı (₺)</label>
                                    <input type="number" id="stockPurchasePrice" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" step="0.01">
                                    </div>

                                    <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı (₺)</label>
                                    <input type="number" id="stockSalePrice" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" step="0.01">
                                    </div>

                                <!-- Geliş Tarihi buraya taşındı -->
                                     <div class="mb-4">
                                     <label class="block text-sm font-medium text-gray-700 mb-1">Geliş Tarihi</label>
                                    <input type="datetime-local" id="arrivalDate" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </div>
                             </div>
                        </div>

                                    </div>
                                </form>
                            `,
                        width: 800,
                            showCancelButton: true,
                            confirmButtonText: 'Kaydet',
                            cancelButtonText: 'İptal',
                            confirmButtonColor: '#d4a373',
                            cancelButtonColor: '#6b7280',
                        didOpen: () => {
                            const productSearch = document.getElementById('productSearch');
                            const searchResults = document.getElementById('searchResults');
                            const selectedProductId = document.getElementById('selectedProductId');
                            const selectedProductInfo = document.getElementById('selectedProductInfo');
                            const selectedProductName = document.getElementById('selectedProductName');
                            const modalBarcodeInput = document.getElementById('modalBarcodeInput');
                            const barcodeSearchResults = document.getElementById('barcodeSearchResults');

                            // Barkod ile arama (modal açıldığında products dizisiyle)
                            if (modalBarcodeInput) {
                                let modalBarcodeTimeout;
                                modalBarcodeInput.addEventListener('input', function() {
                                    clearTimeout(modalBarcodeTimeout);
                                    const barcode = this.value.trim();
                                    if (barcode.length > 0) {
                                        modalBarcodeTimeout = setTimeout(() => {
                                            const filteredProducts = products.filter(product => product.barcode && product.barcode.includes(barcode));
                                            if (filteredProducts.length > 0) {
                                                barcodeSearchResults.innerHTML = '';
                                                filteredProducts.forEach(product => {
                                                    const resultItem = document.createElement('div');
                                                    resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                                    resultItem.textContent = product.name + ' (' + product.barcode + ')';
                                                    resultItem.addEventListener('click', function() {
                                                        selectedProductId.value = product.id;
                                                        productSearch.value = product.name;
                                                        selectedProductInfo.classList.remove('hidden');
                                                        selectedProductName.textContent = product.name;
                                                        modalBarcodeInput.value = product.barcode;
                                                        barcodeSearchResults.classList.add('hidden');
                                                        // Stok bilgileri doldur
                                                        if (product.stock) {
                                                            document.getElementById('stockUnit').value = product.stock.unit;
                                                            if (product.stock.supplier) {
                                                                document.getElementById('stockSupplier').value = product.stock.supplier;
                                                            }
                                                            if (product.stock.purchase_price) {
                                                                document.getElementById('stockPurchasePrice').value = product.stock.purchase_price;
                                                            }
                                                            if (product.stock.sale_price) {
                                                                document.getElementById('stockSalePrice').value = product.stock.sale_price;
                                                            }
                                                        }
                                                    });
                                                    barcodeSearchResults.appendChild(resultItem);
                                                });
                                                barcodeSearchResults.classList.remove('hidden');
                                            } else {
                                                barcodeSearchResults.innerHTML = '<div class="px-4 py-2 text-gray-500">Sonuç bulunamadı</div>';
                                                barcodeSearchResults.classList.remove('hidden');
                                            }
                                        }, 400);
                                    } else {
                                        barcodeSearchResults.classList.add('hidden');
                                    }
                                });
                                document.addEventListener('click', function(e) {
                                    if (!modalBarcodeInput.contains(e.target) && !barcodeSearchResults.contains(e.target)) {
                                        barcodeSearchResults.classList.add('hidden');
                                    }
                                });
                            }
                            
                            // Arama kutusu işlevselliği
                            productSearch.addEventListener('input', function() {
                                const searchTerm = this.value.toLowerCase();
                                if (searchTerm.length < 2) {
                                    searchResults.classList.add('hidden');
                                    return;
                                }
                                
                                const filteredProducts = products.filter(product => 
                                    product.name.toLowerCase().includes(searchTerm)
                                );
                                
                                if (filteredProducts.length > 0) {
                                    searchResults.innerHTML = '';
                                    filteredProducts.forEach(product => {
                                        const resultItem = document.createElement('div');
                                        resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                        resultItem.textContent = product.name;
                                        resultItem.addEventListener('click', function() {
                                            selectedProductId.value = product.id;
                                            productSearch.value = product.name;
                                            searchResults.classList.add('hidden');
                                            selectedProductInfo.classList.remove('hidden');
                                            selectedProductName.textContent = product.name;
                                            
                                            // Eğer ürünün stok bilgisi varsa, birim alanını doldur
                                            if (product.stock) {
                                                document.getElementById('stockUnit').value = product.stock.unit;
                                                
                                                // Supplier/manufacturer'ı doldur (varsa)
                                                if (product.stock.supplier) {
                                                    document.getElementById('stockSupplier').value = product.stock.supplier;
                                                }
                                                if (product.stock.purchase_price) {
                                                    document.getElementById('stockPurchasePrice').value = product.stock.purchase_price;
                                                }
                                                if (product.stock.sale_price) {
                                                    document.getElementById('stockSalePrice').value = product.stock.sale_price;
                                                }
                                            }
                                        });
                                        searchResults.appendChild(resultItem);
                                    });
                                    searchResults.classList.remove('hidden');
                                } else {
                                    searchResults.innerHTML = '<div class="px-4 py-2 text-gray-500">Sonuç bulunamadı</div>';
                                    searchResults.classList.remove('hidden');
                                }
                            });
                            
                            // Sayfa tıklandığında sonuçları kapat
                            document.addEventListener('click', function(e) {
                                if (!productSearch.contains(e.target) && !searchResults.contains(e.target)) {
                                    searchResults.classList.add('hidden');
                                }
                            });
                        },
                            preConfirm: () => {
                            const productId = document.getElementById('selectedProductId').value;
                            const type = document.getElementById('stockType').value;
                                const unit = document.getElementById('stockUnit').value;
                                const quantity = document.getElementById('stockQuantity').value;
                            const description = document.getElementById('stockDescription').value;
                            const supplier = document.getElementById('stockSupplier').value;
                            const purchasePrice = document.getElementById('stockPurchasePrice').value;
                            const salePrice = document.getElementById('stockSalePrice').value;
                            const arrivalDate = document.getElementById('arrivalDate').value;

                                if (!productId) {
                                    Swal.showValidationMessage('Lütfen bir ürün seçin');
                                    return false;
                                }

                            if (!quantity || quantity <= 0) {
                                    Swal.showValidationMessage('Geçerli bir miktar giriniz');
                                    return false;
                                }

                                return fetch('/admin/inventory/stock', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ 
                                    product_id: productId,
                                    type,
                                    unit, 
                                    quantity,
                                    description,
                                    supplier,
                                    purchase_price: purchasePrice,
                                    sale_price: salePrice,
                                    arrival_date: arrivalDate
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Stok işlemi yapılırken bir hata oluştu');
                                }
                                return response.json();
                            })
                                .then(data => {
                                    if (!data.success) {
                                    throw new Error(data.message || 'Stok işlemi yapılırken bir hata oluştu');
                                    }
                                    return data;
                                })
                                .catch(error => {
                                console.error('Stok işlemi hatası:', error);
                                    Swal.showValidationMessage(error.message);
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                Swal.fire({
                                    title: 'Başarılı!',
                                    text: result.value.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        });
                    } else {
                    Swal.fire('Hata!', data.message || 'Ürünler yüklenirken bir hata oluştu', 'error');
                    }
                })
                .catch(error => {
                console.error('Hata:', error);
                Swal.fire('Hata!', error.message || 'Ürünler yüklenirken bir hata oluştu', 'error');
                });
        }

        // Stok güncelleme
        function openStockModal(stockId, productName) {
            Swal.fire({
                title: `${productName} - Stok Güncelle`,
                html: `
                    <form id="editStockForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">İşlem Tipi</label>
                            <select id="stockType" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="giris">Stok Ekle</option>
                                <option value="cikis">Stok Çıkar</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Miktar</label>
                            <input type="number" id="stockQuantity" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1" step="any" value="1">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                            <input type="text" id="stockDescription" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Opsiyonel">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alış Fiyatı (₺)</label>
                            <input type="number" id="purchasePrice" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" step="0.01" placeholder="Opsiyonel">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı (₺)</label>
                            <input type="number" id="salePrice" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="0" step="0.01" placeholder="Opsiyonel">
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Güncelle',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#6b7280',
                preConfirm: () => {
                    const type = document.getElementById('stockType').value;
                    const quantity = document.getElementById('stockQuantity').value;
                    const description = document.getElementById('stockDescription').value;
                    const purchasePrice = document.getElementById('purchasePrice').value;
                    const salePrice = document.getElementById('salePrice').value;

                    if (!quantity || quantity <= 0) {
                        Swal.showValidationMessage('Geçerli bir miktar giriniz');
                        return false;
                    }

                    return fetch(`/admin/inventory/${stockId}/stock`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            type, 
                            quantity, 
                            description,
                            purchase_price: purchasePrice,
                            sale_price: salePrice
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Stok güncellenirken bir hata oluştu');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Stok güncellenirken bir hata oluştu');
                        }
                        return data;
                    })
                    .catch(error => {
                        console.error('Stok güncelleme hatası:', error);
                        Swal.showValidationMessage(error.message);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: result.value.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Stok hareketleri detayı
        function showStockMovements(stockId, productName) {
            fetch(`/admin/inventory/${stockId}/movements`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const movements = data.movements.map(m => `
                            <div class="border-b border-gray-200 py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-600">${m.description || 'Stok hareketi'}</p>
                                        <p class="text-xs text-gray-500">${new Date(m.created_at).toLocaleString()}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-medium ${m.type === 'giris' ? 'text-green-600' : 'text-red-600'}">
                                            ${m.type === 'giris' ? '+' : '-'}${m.quantity}
                                        </span>
                                        ${m.purchase_price ? `<p class="text-xs text-gray-500">Alış: ${parseFloat(m.purchase_price).toFixed(2)} ₺</p>` : ''}
                                        ${m.sale_price ? `<p class="text-xs text-gray-500">Satış: ${parseFloat(m.sale_price).toFixed(2)} ₺</p>` : ''}
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        if (movements === '') {
                            movements = '<p class="text-center text-gray-500 py-4">Henüz stok hareketi bulunmuyor.</p>';
                        }

                        Swal.fire({
                            title: `${productName} - Stok Hareketleri`,
                            html: `<div class="mt-4 max-h-96 overflow-y-auto">${movements}</div>`,
                            width: 600,
                            showConfirmButton: false,
                            showCloseButton: true
                        });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Hata!',
                        text: error.message || 'Stok hareketleri alınırken bir hata oluştu',
                        icon: 'error'
                    });
                });
        }

        // Grafik verilerini hazırla ve oluştur
        document.addEventListener('DOMContentLoaded', function() {
            const stockData = {!! json_encode($chartMovements) !!};

            // Grafik verilerini düzenle
            const labels = Object.keys(stockData).reverse();
            const girisData = Object.values(stockData).map(day => day.giris).reverse();
            const cikisData = Object.values(stockData).map(day => day.cikis).reverse();

            // Grafik oluştur
            const ctx = document.getElementById('stockMovementsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Giriş Miktarı',
                            data: girisData,
                            backgroundColor: '#34D399',
                            borderColor: '#34D399',
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Çıkış Miktarı',
                            data: cikisData,
                            backgroundColor: '#F87171',
                            borderColor: '#F87171',
                            borderWidth: 1,
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: '#f0f0f0'
                            },
                            title: {
                                display: true,
                                text: 'Ürün Miktarı',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Tarih',
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            padding: 10,
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#000',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyColor: '#666',
                            bodyFont: {
                                size: 12
                            },
                            borderColor: '#ddd',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.parsed.y + ' adet';
                                    return label;
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        });

        // Üreticiler Modalı
        function openSuppliersModal() {
            fetch('/admin/suppliers')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const suppliers = data.suppliers;
                        Swal.fire({
                            title: 'Tedarikçiler',
                            width: '800px',
                            html: `
                                <div class="h-[600px] flex flex-col">
                                    <div class="mb-4 flex justify-between items-center px-4">
                                        <div class="relative w-64">
                                            <input type="text" id="supplierSearch" placeholder="Tedarikçi ara..." 
                                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                                            <div class="absolute left-3 top-2.5 text-gray-400">
                                                <i class="fas fa-search"></i>
                                            </div>
                                        </div>
                                        <button type="button" onclick="openNewSupplierForm()" class="px-4 py-2 bg-[#d4a373] text-white rounded-md hover:bg-[#c48c63] transition-colors">
                                            <i class="fas fa-plus mr-2"></i>Yeni Tedarikçi
                                        </button>
                                    </div>
                                    <div class="flex-1 overflow-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortSuppliers('name')">
                                                        İSİM <i class="fas fa-sort ml-1"></i>
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        İLETİŞİM KİŞİSİ
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        TELEFON
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        E-POSTA
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        İŞLEMLER
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="suppliersTableBody" class="bg-white divide-y divide-gray-200">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4 flex justify-between items-center px-4 py-2 border-t">
                                        <div class="text-sm text-gray-500">
                                            Toplam <span id="totalSuppliers">0</span> tedarikçi
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button id="prevPageSupplier" class="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span id="pageInfoSupplier" class="text-sm">Sayfa <span id="currentPageSupplier">1</span>/<span id="totalPagesSupplier">1</span></span>
                                            <button id="nextPageSupplier" class="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            showCloseButton: true,
                            didOpen: () => {
                                window.suppliersState = {
                                    suppliers: suppliers,
                                    filteredSuppliers: suppliers,
                                    currentPage: 1,
                                    itemsPerPage: 8,
                                    sortField: 'name',
                                    sortDirection: 'asc'
                                };
                                const searchInput = document.getElementById('supplierSearch');
                                searchInput.addEventListener('input', (e) => {
                                    const searchTerm = e.target.value.toLowerCase();
                                    window.suppliersState.filteredSuppliers = window.suppliersState.suppliers.filter(m => 
                                        m.name.toLowerCase().includes(searchTerm) ||
                                        (m.contact_person && m.contact_person.toLowerCase().includes(searchTerm)) ||
                                        (m.email && m.email.toLowerCase().includes(searchTerm))
                                    );
                                    window.suppliersState.currentPage = 1;
                                    updateSuppliersTable();
                                });
                                document.getElementById('prevPageSupplier').addEventListener('click', () => {
                                    if (window.suppliersState.currentPage > 1) {
                                        window.suppliersState.currentPage--;
                                        updateSuppliersTable();
                                    }
                                });
                                document.getElementById('nextPageSupplier').addEventListener('click', () => {
                                    const totalPages = Math.ceil(window.suppliersState.filteredSuppliers.length / window.suppliersState.itemsPerPage);
                                    if (window.suppliersState.currentPage < totalPages) {
                                        window.suppliersState.currentPage++;
                                        updateSuppliersTable();
                                    }
                                });
                                updateSuppliersTable();
                            }
                        });
                    } else {
                        Swal.fire('Hata!', data.message || 'Tedarikçiler yüklenirken bir hata oluştu', 'error');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire('Hata!', 'Tedarikçiler yüklenirken bir hata oluştu', 'error');
                });
        }

        // Yeni üretici ekleme formunu açma fonksiyonu
        function openNewSupplierForm() {
            Swal.fire({
                title: 'Yeni Tedarikçi Ekle',
                html: `
                    <form id="addSupplierForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label>
                            <input type="text" id="supplierName" class="w-full px-3 py-2 border border-gray-300 rounded-md" required minlength="2" maxlength="100">
                            <div id="supplierNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">İletişim Kişisi</label>
                            <input type="text" id="supplierContact" class="w-full px-3 py-2 border border-gray-300 rounded-md" minlength="2" maxlength="100">
                            <div id="supplierContactError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" id="supplierPhone" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="5XX XXX XXXX">
                            <div id="supplierPhoneError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <input type="email" id="supplierEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <div id="supplierEmailError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <textarea id="supplierAddress" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="3" maxlength="255"></textarea>
                            <div id="supplierAddressError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                            <textarea id="supplierNotes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" maxlength="500"></textarea>
                            <div id="supplierNotesError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                    </form>
                `,
                didOpen: () => {
                    const phoneInput = document.getElementById('supplierPhone');
                    phoneInput.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.startsWith('0')) {
                            value = value.substring(1);
                        }
                        if (value.length > 10) {
                            value = value.slice(0, 10);
                        }
                        if (value.length >= 3) {
                            value = value.slice(0, 3) + ' ' + value.slice(3);
                        }
                        if (value.length >= 7) {
                            value = value.slice(0, 7) + ' ' + value.slice(7);
                        }
                        e.target.value = value;
                    });
                    const form = document.getElementById('addSupplierForm');
                    const inputs = form.querySelectorAll('input, textarea');
                    inputs.forEach(input => {
                        input.addEventListener('input', function() {
                            validateSupplierInput(this);
                        });
                        input.addEventListener('blur', function() {
                            validateSupplierInput(this);
                        });
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Ekle',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#6B7280',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const name = document.getElementById('supplierName').value;
                    const contact = document.getElementById('supplierContact').value;
                    const phone = document.getElementById('supplierPhone').value;
                    const email = document.getElementById('supplierEmail').value;
                    const address = document.getElementById('supplierAddress').value;
                    const notes = document.getElementById('supplierNotes').value;
                    let isValid = true;
                    let errorMessage = '';
                    if (!name || name.length < 2) {
                        isValid = false;
                        errorMessage = 'Tedarikçi adı en az 2 karakter olmalıdır';
                        document.getElementById('supplierNameError').textContent = errorMessage;
                        document.getElementById('supplierNameError').classList.remove('hidden');
                    }
                    if (contact && contact.length < 2) {
                        isValid = false;
                        errorMessage = 'İletişim kişisi adı en az 2 karakter olmalıdır';
                        document.getElementById('supplierContactError').textContent = errorMessage;
                        document.getElementById('supplierContactError').classList.remove('hidden');
                    }
                    if (phone && !isValidPhone(phone)) {
                        isValid = false;
                        errorMessage = 'Geçerli bir telefon numarası giriniz';
                        document.getElementById('supplierPhoneError').textContent = errorMessage;
                        document.getElementById('supplierPhoneError').classList.remove('hidden');
                    }
                    if (email && !isValidEmail(email)) {
                        isValid = false;
                        errorMessage = 'Geçerli bir e-posta adresi giriniz';
                        document.getElementById('supplierEmailError').textContent = errorMessage;
                        document.getElementById('supplierEmailError').classList.remove('hidden');
                    }
                    if (!isValid) {
                        Swal.showValidationMessage(errorMessage);
                        return false;
                    }
                    return fetch('/admin/suppliers', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name,
                            contact_person: contact,
                            phone: phone.replace(/\D/g, ''),
                            email,
                            address,
                            notes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Tedarikçi eklenirken bir hata oluştu');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(error.message);
                        return false;
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    window.suppliersState.suppliers.push(result.value.supplier);
                    window.suppliersState.filteredSuppliers = window.suppliersState.suppliers;
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Tedarikçi başarıyla eklendi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        openSuppliersModal();
                        updateSuppliersTable();
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    openSuppliersModal();
                }
            });
        }

        // Validasyon yardımcı fonksiyonları
        function validateInput(input) {
            const errorDiv = document.getElementById(`${input.id}Error`);
            let isValid = true;
            let errorMessage = '';

            switch(input.id) {
                case 'manufacturerName':
                    if (!input.value) {
                        isValid = false;
                        errorMessage = 'Üretici adı zorunludur';
                    } else if (input.value.length < 2) {
                        isValid = false;
                        errorMessage = 'Üretici adı en az 2 karakter olmalıdır';
                    }
                    break;
                case 'manufacturerContact':
                    if (input.value && input.value.length < 2) {
                        isValid = false;
                        errorMessage = 'İletişim kişisi adı en az 2 karakter olmalıdır';
                    }
                    break;
                case 'manufacturerPhone':
                    if (input.value && !isValidPhone(input.value)) {
                        isValid = false;
                        errorMessage = 'Geçerli bir telefon numarası giriniz';
                    }
                    break;
                case 'manufacturerEmail':
                    if (input.value && !isValidEmail(input.value)) {
                        isValid = false;
                        errorMessage = 'Geçerli bir e-posta adresi giriniz';
                    }
                    break;
            }

            if (!isValid) {
                errorDiv.textContent = errorMessage;
                errorDiv.classList.remove('hidden');
                input.classList.add('border-red-500');
            } else {
                errorDiv.classList.add('hidden');
                input.classList.remove('border-red-500');
            }

            return isValid;
        }

        function isValidPhone(phone) {
            const phoneRegex = /^5[0-9]{2}\s[0-9]{3}\s[0-9]{4}$/;
            return phoneRegex.test(phone);
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Üreticiler tablosunu güncelleme fonksiyonu
        function updateSuppliersTable() {
            const state = window.suppliersState;
            const startIndex = (state.currentPage - 1) * state.itemsPerPage;
            const endIndex = startIndex + state.itemsPerPage;
            // Sıralama
            const sortedSuppliers = [...state.filteredSuppliers].sort((a, b) => {
                const aValue = a[state.sortField] || '';
                const bValue = b[state.sortField] || '';
                return state.sortDirection === 'asc' 
                    ? aValue.localeCompare(bValue)
                    : bValue.localeCompare(aValue);
            });
            const pageSuppliers = sortedSuppliers.slice(startIndex, endIndex);
            const tbody = document.getElementById('suppliersTableBody');
            if (!tbody) return;
            tbody.innerHTML = pageSuppliers.map(supplier => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${supplier.name}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${supplier.contact_person || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${supplier.phone || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${supplier.email || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button onclick="editSupplier(${supplier.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteSupplier(${supplier.id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            // Sayfalama bilgilerini güncelle
            const totalPages = Math.ceil(state.filteredSuppliers.length / state.itemsPerPage);
            document.getElementById('currentPageSupplier').textContent = state.currentPage;
            document.getElementById('totalPagesSupplier').textContent = totalPages;
            document.getElementById('totalSuppliers').textContent = state.filteredSuppliers.length;
            
            // Sayfalama butonlarının durumunu güncelle
            document.getElementById('prevPageSupplier').disabled = state.currentPage === 1;
            document.getElementById('nextPageSupplier').disabled = state.currentPage === totalPages;
        }

        // Sıralama fonksiyonu
        function sortSuppliers(field) {
            if (window.suppliersState.sortField === field) {
                window.suppliersState.sortDirection = window.suppliersState.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                window.suppliersState.sortField = field;
                window.suppliersState.sortDirection = 'asc';
            }
            updateSuppliersTable();
        }

        // Üreticileri yükle
        function loadSuppliers() {
            // Select elementinin varlığını kontrol et
            const supplierSelect = document.getElementById('stockSupplier');
            if (!supplierSelect) {
                return; // Select elementi yoksa fonksiyondan çık
            }

            fetch('/admin/suppliers', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    supplierSelect.innerHTML = '<option value="">Tedarikçi Seçin</option>';
                    
                    data.suppliers.forEach(supplier => {
                        if (supplier.is_active) {
                            const option = new Option(supplier.name, supplier.id);
                            supplierSelect.add(option);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Tedarikçiler yüklenirken hata:', error);
            });
        }

        // Sayfa yüklendiğinde üreticileri yükle
        document.addEventListener('DOMContentLoaded', function() {
            // Stok ekleme modalı açıldığında üreticileri yükle
            const originalOpenAddStockModal = window.openAddStockModal;
            window.openAddStockModal = function() {
                originalOpenAddStockModal();
                loadSuppliers();
            };
        });

        // Tedarikçi Düzenleme
        function editSupplier(id) {
            fetch(`/admin/suppliers/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const supplier = data.supplier;
                        Swal.fire({
                            title: 'Tedarikçi Düzenle',
                            html: `
                                <form id="editSupplierForm" class="text-left">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label>
                                        <input type="text" id="supplierName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${supplier.name}" required minlength="2" maxlength="100">
                                        <div id="supplierNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">İletişim Kişisi</label>
                                        <input type="text" id="contactPerson" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${supplier.contact_person || ''}" minlength="2" maxlength="100">
                                        <div id="contactPersonError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                        <input type="tel" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${supplier.phone || ''}" placeholder="5XX XXX XXXX">
                                        <div id="phoneError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                        <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${supplier.email || ''}">
                                        <div id="emailError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                                        <textarea id="address" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" maxlength="255">${supplier.address || ''}</textarea>
                                        <div id="addressError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" id="isActive" class="form-checkbox h-4 w-4 text-[#d4a373]" ${supplier.is_active ? 'checked' : ''}>
                                            <span class="ml-2">Aktif</span>
                                        </label>
                                    </div>
                                </form>
                            `,
                            didOpen: () => {
                                // Telefon maskeleme
                                const phoneInput = document.getElementById('phone');
                                phoneInput.addEventListener('input', function(e) {
                                    let value = e.target.value.replace(/\D/g, '');
                                    if (value.startsWith('0')) {
                                        value = value.substring(1);
                                    }
                                    if (value.length > 10) {
                                        value = value.slice(0, 10);
                                    }
                                    if (value.length >= 3) {
                                        value = value.slice(0, 3) + ' ' + value.slice(3);
                                    }
                                    if (value.length >= 7) {
                                        value = value.slice(0, 7) + ' ' + value.slice(7);
                                    }
                                    e.target.value = value;
                                });

                                // Form validasyonları
                                const form = document.getElementById('editSupplierForm');
                                const inputs = form.querySelectorAll('input:not([type="checkbox"]), textarea');
                                
                                inputs.forEach(input => {
                                    input.addEventListener('input', function() {
                                        validateInput(this);
                                    });

                                    input.addEventListener('blur', function() {
                                        validateInput(this);
                                    });
                                });
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Güncelle',
                            cancelButtonText: 'İptal',
                            confirmButtonColor: '#d4a373',
                            cancelButtonColor: '#6b7280',
                            preConfirm: () => {
                                const name = document.getElementById('supplierName').value;
                                const contactPerson = document.getElementById('contactPerson').value;
                                const phone = document.getElementById('phone').value;
                                const email = document.getElementById('email').value;
                                const address = document.getElementById('address').value;
                                const isActive = document.getElementById('isActive').checked;

                                // Validasyon kontrolleri
                                let isValid = true;
                                let errorMessage = '';

                                if (!name || name.length < 2) {
                                    isValid = false;
                                    errorMessage = 'Tedarikçi adı en az 2 karakter olmalıdır';
                                    document.getElementById('supplierNameError').textContent = errorMessage;
                                    document.getElementById('supplierNameError').classList.remove('hidden');
                                }

                                if (contactPerson && contactPerson.length < 2) {
                                    isValid = false;
                                    errorMessage = 'İletişim kişisi adı en az 2 karakter olmalıdır';
                                    document.getElementById('contactPersonError').textContent = errorMessage;
                                    document.getElementById('contactPersonError').classList.remove('hidden');
                                }

                                if (phone && !isValidPhone(phone)) {
                                    isValid = false;
                                    errorMessage = 'Geçerli bir telefon numarası giriniz';
                                    document.getElementById('phoneError').textContent = errorMessage;
                                    document.getElementById('phoneError').classList.remove('hidden');
                                }

                                if (email && !isValidEmail(email)) {
                                    isValid = false;
                                    errorMessage = 'Geçerli bir e-posta adresi giriniz';
                                    document.getElementById('emailError').textContent = errorMessage;
                                    document.getElementById('emailError').classList.remove('hidden');
                                }

                                if (!isValid) {
                                    Swal.showValidationMessage(errorMessage);
                                    return false;
                                }

                                return fetch(`/admin/suppliers/${id}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        name,
                                        contact_person: contactPerson,
                                        phone: phone.replace(/\D/g, ''),
                                        email,
                                        address,
                                        is_active: isActive
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (!data.success) {
                                        throw new Error(data.message || 'Tedarikçi güncellenirken bir hata oluştu');
                                    }
                                    return data;
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                // Başarı bildirimi göster
                                Swal.fire({
                                    title: 'Başarılı!',
                                    text: 'Tedarikçi başarıyla güncellendi',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Bildirim kapandıktan sonra üreticiler listesine dön
                                    openSuppliersModal();
                                });
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // İptal edildiğinde üreticiler listesine dön
                                openSuppliersModal();
                            }
                        });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire('Hata!', 'Tedarikçi bilgileri alınırken bir hata oluştu.', 'error');
                });
        }

        // Tedarikçi Silme
        function deleteSupplier(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu tedarikçiyi silmek istediğinizden emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/suppliers/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: 'Tedarikçi başarıyla silindi',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                openSuppliersModal();
                            });
                        } else {
                            Swal.fire('Hata!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        Swal.fire('Hata!', 'Tedarikçi silinirken bir hata oluştu.', 'error');
                    });
                }
            });
        }

        // Stok ekleme formunda tedarikçi seçimini güncelle
        function updateSupplierSelect() {
            fetch('/admin/suppliers')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const suppliers = data.suppliers;
                        const select = document.getElementById('stockSupplier');
                        if (select) {
                            select.innerHTML = '<option value="">Tedarikçi Seçin</option>';
                            suppliers.forEach(supplier => {
                                if (supplier.is_active) {
                                    select.innerHTML += `<option value="${supplier.id}">${supplier.name}</option>`;
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Tedarikçiler yüklenirken hata:', error);
                });
        }

        // Stok ekleme modalında tedarikçi seçimini güncelle
        document.addEventListener('DOMContentLoaded', function() {
            const originalOpenAddStockModal = window.openAddStockModal;
            window.openAddStockModal = function() {
                originalOpenAddStockModal();
                updateSupplierSelect();
            };
        });

        // Eksik olan fonksiyon: validateSupplierInput
        function validateSupplierInput(input) {
            return validateInput(input);
        }

        // Barkod arama işlevi
        const barcodeInput = document.getElementById('barcodeInput');
        if (barcodeInput) {
            let barcodeTimeout;
            barcodeInput.addEventListener('input', function() {
                clearTimeout(barcodeTimeout);
                const barcode = this.value.trim();
                
                if (barcode.length > 0) {
                    barcodeTimeout = setTimeout(() => {
                        fetch(`/admin/inventory/barcode/${barcode}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.product) {
                                    const productRows = document.querySelectorAll('.product-row');
                                    productRows.forEach(row => {
                                        const productId = row.querySelector('td:first-child').textContent.trim();
                                        row.style.display = productId === data.product.name ? '' : 'none';
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Ürün Bulunamadı',
                                        text: 'Bu barkoda sahip ürün bulunamadı.',
                                        icon: 'info',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Barkod arama hatası:', error);
                                Swal.fire({
                                    title: 'Hata!',
                                    text: 'Barkod araması sırasında bir hata oluştu.',
                                    icon: 'error',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            });
                    }, 500); // 500ms gecikme ile arama yap
                } else {
                    // Barkod alanı boşsa tüm ürünleri göster
                    const productRows = document.querySelectorAll('.product-row');
                    productRows.forEach(row => {
                        row.style.display = '';
                    });
                }
            });
        }

        // Barkod ile arama (dropdown ile)
        const modalBarcodeInput = document.getElementById('modalBarcodeInput');
        const barcodeSearchResults = document.getElementById('barcodeSearchResults');
        if (modalBarcodeInput) {
            let modalBarcodeTimeout;
            modalBarcodeInput.addEventListener('input', function() {
                clearTimeout(modalBarcodeTimeout);
                const barcode = this.value.trim();
                if (barcode.length > 0) {
                    modalBarcodeTimeout = setTimeout(() => {
                        fetch('/admin/inventory/products', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.products) {
                                const filteredProducts = data.products.filter(product => product.barcode && product.barcode.includes(barcode));
                                if (filteredProducts.length > 0) {
                                    barcodeSearchResults.innerHTML = '';
                                    filteredProducts.forEach(product => {
                                        const resultItem = document.createElement('div');
                                        resultItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                                        resultItem.textContent = product.name + ' (' + product.barcode + ')';
                                        resultItem.addEventListener('click', function() {
                                            selectedProductId.value = product.id;
                                            productSearch.value = product.name;
                                            selectedProductInfo.classList.remove('hidden');
                                            selectedProductName.textContent = product.name;
                                            modalBarcodeInput.value = product.barcode;
                                            barcodeSearchResults.classList.add('hidden');
                                            // Stok bilgileri doldur
                                            if (product.stock) {
                                                document.getElementById('stockUnit').value = product.stock.unit;
                                                if (product.stock.supplier) {
                                                    document.getElementById('stockSupplier').value = product.stock.supplier;
                                                }
                                                if (product.stock.purchase_price) {
                                                    document.getElementById('stockPurchasePrice').value = product.stock.purchase_price;
                                                }
                                                if (product.stock.sale_price) {
                                                    document.getElementById('stockSalePrice').value = product.stock.sale_price;
                                                }
                                            }
                                        });
                                        barcodeSearchResults.appendChild(resultItem);
                                    });
                                    barcodeSearchResults.classList.remove('hidden');
                                } else {
                                    barcodeSearchResults.innerHTML = '<div class="px-4 py-2 text-gray-500">Sonuç bulunamadı</div>';
                                    barcodeSearchResults.classList.remove('hidden');
                                }
                            }
                        });
                    }, 400);
                } else {
                    barcodeSearchResults.classList.add('hidden');
                }
            });
            document.addEventListener('click', function(e) {
                if (!modalBarcodeInput.contains(e.target) && !barcodeSearchResults.contains(e.target)) {
                    barcodeSearchResults.classList.add('hidden');
                }
            });
        }

        function editSupplierForStock(stockId, currentSupplier) {
            fetch('/admin/suppliers', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let options = '<option value="">Tedarikçi Seçin</option>';
                    data.suppliers.forEach(supplier => {
                        if (supplier.is_active) {
                            options += `<option value="${supplier.name}" ${supplier.name === currentSupplier ? 'selected' : ''}>${supplier.name}</option>`;
                        }
                    });
                    Swal.fire({
                        title: 'Tedarikçi Düzenle',
                        html: `<select id=\'newSupplier\' class=\'swal2-input\'>${options}</select>`,
                        showCancelButton: true,
                        confirmButtonText: 'Kaydet',
                        cancelButtonText: 'İptal',
                        preConfirm: () => {
                            const newSupplier = document.getElementById('newSupplier').value;
                            return fetch(`/admin/stocks/${stockId}/update-supplier`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ supplier: newSupplier })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message);
                                }
                                return data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(error.message);
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: 'Tedarikçi güncellendi',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
</body>
</html> 