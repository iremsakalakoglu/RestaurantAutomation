<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Garson Paneli</title>
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
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-[#f5e6d3] p-4 shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk
                <span class="text-gray-600 text-lg">Garson</span>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleView('active')" class="text-[#d4a373] hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-fire mr-2"></i>Aktif Siparişler
                </button>
                <button onclick="toggleView('tables')" class="text-gray-600 hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-table mr-2"></i>Masalarım
                </button>
                <button onclick="toggleView('delivered')" class="text-gray-600 hover:text-[#d4a373] transition-colors px-3 py-1 rounded-md">
                    <i class="fas fa-check-circle mr-2"></i>Teslim Edilenler
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
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Sorumlu Olduğum Masalar</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $assignedTables->count() }}</h3>
                    </div>
                    <div class="text-blue-400">
                        <i class="fas fa-table text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Aktif Siparişler</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $activeOrders->count() }}</h3>
                    </div>
                    <div class="text-yellow-400">
                        <i class="fas fa-fire text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-400">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Dolu Masalar</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $assignedTables->where('status', 'dolu')->count() }}</h3>
                    </div>
                    <div class="text-green-400">
                        <i class="fas fa-chair text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masalarım Bölümü -->
        <div id="tablesSection" class="mb-8" style="display: none;">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b flex justify-between items-center bg-[#f5e6d3]">
                    <h2 class="text-2xl font-semibold text-[#d4a373]">Sorumlu Olduğum Masalar</h2>
                </div>
                <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($assignedTables as $table)
                            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 
                                @if($table->status == 'dolu') border-red-500
                                @elseif($table->status == 'boş') border-green-500
                        @endif">
                        <div class="flex flex-col h-full">
                            <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold">Masa {{ $table->table_number }}</h3>
                                <span class="px-2 py-1 rounded-full text-xs
                                    @if($table->status == 'dolu') bg-red-100 text-red-800
                                    @elseif($table->status == 'boş') bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($table->status) }}
                                </span>
                            </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $table->capacity }} Kişilik</p>
                            @if($table->currentOrder)
                                        <div class="mt-auto">
                                            <form action="{{ route('waiter.order.close', ['order' => $table->currentOrder->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                    class="w-full bg-[#d4a373] hover:bg-[#c48c63] text-white px-3 py-2 rounded text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    Adisyonu Kapat
                                                </button>
                                            </form>
                                </div>
                                    @else
                                        @if($table->status == 'dolu')
                                <div class="mt-auto">
                                                <form action="{{ route('waiter.table.clear', ['table' => $table->id]) }}" method="POST">
                                        @csrf
                                                    <button type="submit" 
                                                        class="w-full bg-[#d4a373] hover:bg-[#c48c63] text-white px-3 py-2 rounded text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                        </svg>
                                                        Masayı Boşalt
                                        </button>
                                    </form>
                                </div>
                                @elseif($table->status == 'boş')
                                <div class="mt-auto">
                                    <button type="button" onclick="openCreateOrderModal({{ $table->id }})" class="w-full bg-[#d4a373] hover:bg-[#c48c63] text-white px-3 py-2 rounded text-sm font-medium transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Sipariş Oluştur
                                    </button>
                                </div>
                            @else
                                            <p class="text-sm text-gray-500 text-center">Aktif sipariş yok</p>
                                        @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
            </div>
        </div>

        <!-- Aktif Siparişler Bölümü -->
        <div id="activeOrdersSection" class="mb-8">
        <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b flex justify-between items-center bg-[#f5e6d3]">
                    <h2 class="text-2xl font-semibold text-[#d4a373]">Aktif Siparişler</h2>
                    <div class="text-sm text-gray-600">
                        Toplam: {{ $activeOrders->count() }} Sipariş
                    </div>
            </div>
            <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($activeOrders as $order)
                            <div class="order-card bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                                <div class="bg-[#fdf5ed] p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-3 mb-1">
                                                <span class="text-xl font-bold text-[#b58863]">Sipariş #{{ $order->id }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-sm font-medium
                                                    @if($order->status == 'hazırlanıyor') bg-blue-50 text-blue-700
                                                    @elseif($order->status == 'hazır') bg-green-50 text-green-700
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                                <i class="fas fa-table text-[#d4a373]"></i>
                                                <span>Masa {{ $order->table->table_number }}</span>
                                                <span class="mx-2 text-gray-300">•</span>
                                                <i class="far fa-clock text-[#d4a373]"></i>
                                                <span>{{ $order->created_at->format('H:i') }}</span>
                                                <span class="text-xs text-gray-400">({{ $order->created_at->diffForHumans(['parts' => 1]) }})</span>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            
                                <div class="divide-y flex-1">
                                        @foreach($order->orderDetails as $detail)
                                        <div class="flex justify-between items-center p-3 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center gap-3 flex-1">
                                                <div class="w-8 h-8 flex items-center justify-center rounded-full {{ !$detail->is_ready ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }} font-medium">
                                                    {{ $detail->quantity }}
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-base font-medium text-gray-900">{{ $detail->product->name }}</h4>
                                                    @if($detail->product->description)
                                                        <p class="text-sm text-gray-500">{{ $detail->product->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                @if($detail->is_delivered)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Teslim Edildi
                                                    </span>
                                                @elseif($detail->is_ready)
                                                    <div class="flex gap-1.5">
                                                        <form action="{{ route('waiter.orders.update.delivery', ['id' => $detail->id]) }}" method="POST" class="inline delivery-form">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-2.5 py-0.5 border border-blue-600 rounded-full text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                                Teslim Edildi
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('waiter.order-detail.cancel', ['id' => $detail->id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-2.5 py-0.5 border border-red-600 rounded-full text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                                İptal
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        Hazırlanıyor
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                </div>

                                <div class="bg-gray-50 px-4 py-3 border-t mt-auto">
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="flex items-center gap-4 text-gray-500">
                                            <span>
                                                <i class="fas fa-list-ul text-[#d4a373] mr-1"></i>
                                                Toplam: {{ $order->orderDetails->count() }}
                                            </span>
                                            <span>
                                                <i class="fas fa-check-circle text-[#d4a373] mr-1"></i>
                                                Teslim: {{ $order->orderDetails->where('is_delivered', true)->count() }}
                                            </span>
                                            <span>
                                                <i class="fas fa-clock text-[#d4a373] mr-1"></i>
                                                Bekleyen: {{ $order->orderDetails->where('is_delivered', false)->count() }}
                                            </span>
                                        </div>
                                        <button onclick="openAddProductModal({{ $order->id }})" class="inline-flex items-center px-3 py-1 bg-[#d4a373] text-white rounded-full text-sm font-medium hover:bg-[#b58863] transition-colors">
                                            <i class="fas fa-plus mr-1.5"></i>
                                            Ürün Ekle
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-lg font-medium">Aktif sipariş bulunmamaktadır.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
                                </div>

        <!-- Teslim Edilen Siparişler Bölümü -->
        <div id="deliveredOrdersSection" class="mb-8" style="display: none;">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-6 border-b flex justify-between items-center bg-[#f5e6d3]">
                    <h2 class="text-2xl font-semibold text-[#d4a373]">Teslim Edilen Siparişler</h2>
                    <div class="text-sm text-gray-600">
                        Son 20 Sipariş
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($deliveredOrders as $order)
                            <div class="order-card bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                                <div class="bg-gray-50 p-4 border-b">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl font-bold text-[#d4a373]">Sipariş #{{ $order->id }}</span>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Teslim Edildi
                                            </span>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span class="text-sm font-medium text-gray-900">Masa {{ $order->table->table_number }}</span>
                                            <span class="text-sm text-gray-500">{{ $order->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="divide-y flex-1">
                                    @foreach($order->orderDetails as $detail)
                                        <div class="flex justify-between items-center p-4 hover:bg-gray-50 transition-colors">
                                            <div class="flex items-center gap-4 flex-1">
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-medium text-gray-900">{{ $detail->product->name }}</h4>
                                                    <p class="text-sm text-gray-500">Miktar: {{ $detail->quantity }}</p>
                                                </div>
                                                @if($detail->product->description)
                                                    <span class="text-sm text-gray-500 hidden md:block">{{ $detail->product->description }}</span>
                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Teslim Edildi
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="bg-gray-50 p-4 border-t mt-auto">
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm text-gray-500">
                                            Toplam Ürün: {{ $order->orderDetails->count() }}
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="text-sm text-gray-500">
                                                Teslim Tarihi: {{ $order->updated_at->format('d.m.Y H:i') }}
                                            </div>
                                            <button onclick="openAddProductModal({{ $order->id }})" class="inline-flex items-center px-3 py-1 bg-[#d4a373] text-white rounded-full text-sm font-medium hover:bg-[#b58863] transition-colors">
                                                <i class="fas fa-plus mr-1.5"></i>
                                                Ürün Ekle
                                            </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                            <div class="col-span-2 text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-lg font-medium">Henüz teslim edilen sipariş bulunmamaktadır.</p>
                            </div>
                        @endforelse
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ürün Ekleme Modal -->
    <div id="addProductModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 h-[80vh] flex flex-col" onclick="event.stopPropagation()">
            <div class="p-4 border-b flex justify-between items-center bg-[#f5e6d3]">
                <h3 class="text-lg font-semibold text-[#d4a373]">Siparişe Ürün Ekle</h3>
                <button onclick="closeAddProductModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="addProductForm" action="" method="POST" class="flex-1 flex flex-col h-full" onclick="event.stopPropagation()">
                @csrf
                <div class="flex-1 flex flex-col">
                    <!-- Arama ve Sık Kullanılanlar -->
                    <div class="p-4 border-b">
                        <div class="relative">
                            <input type="text" 
                                id="productSearch" 
                                placeholder="Ürün ara..." 
                                class="w-full rounded-md border-gray-300 pl-10 pr-4 py-2 shadow-sm focus:border-[#d4a373] focus:ring focus:ring-[#d4a373] focus:ring-opacity-50"
                                autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Ana İçerik -->
                    <div class="flex-1 flex overflow-hidden">
                        <!-- Kategoriler -->
                        <div class="w-1/4 border-r overflow-y-auto">
                            <div class="p-2">
                                <button type="button" 
                                    class="category-btn w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors"
                                    data-category="all">
                                    Tüm Ürünler
                                </button>
                                @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                <button type="button" 
                                    class="category-btn w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors"
                                    data-category="{{ $category->id }}">
                                    {{ $category->name }}
                                </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Ürünler -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <div id="productGrid" class="grid grid-cols-2 gap-4">
                                @foreach(\App\Models\Product::with(['category', 'stock'])->orderBy('name')->get() as $product)
                                <div class="product-item border rounded-lg p-3 cursor-pointer hover:border-[#d4a373] transition-colors"
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->name }}"
                                     data-price="{{ number_format($product->current_price, 2) }}"
                                     data-stock="{{ $product->stock->quantity ?? 0 }}"
                                     data-category="{{ $product->category_id }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ number_format($product->current_price, 2) }}₺</p>
                                        </div>
                                        <span class="text-sm px-2 py-1 rounded-full {{ ($product->stock && $product->stock->quantity > 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            Stok: {{ $product->stock->quantity ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seçili Ürün ve Miktar -->
                <div class="border-t bg-gray-50 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <div id="selectedProductInfo" class="text-gray-500">
                            Henüz ürün seçilmedi
                        </div>
                        <div id="quantitySection" class="hidden items-center space-x-2">
                            <button type="button" onclick="decrementQuantity()" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                id="quantity" 
                                name="quantity" 
                                min="1" 
                                value="1" 
                                class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-[#d4a373] focus:ring focus:ring-[#d4a373] focus:ring-opacity-50">
                            <button type="button" onclick="incrementQuantity()" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" id="selectedProduct" name="product_id">
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddProductModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            İptal
                        </button>
                        <button type="submit" id="submitButton" disabled 
                            class="px-4 py-2 bg-[#d4a373] border border-transparent rounded-md text-sm font-medium text-white hover:bg-[#b58863] disabled:opacity-50 disabled:cursor-not-allowed">
                            Ekle
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sipariş Oluşturma Modal -->
    <div id="createOrderModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 h-[80vh] flex flex-col">
            <div class="p-4 border-b flex justify-between items-center bg-[#f5e6d3]">
                <h3 class="text-lg font-semibold text-[#d4a373]">Yeni Sipariş Oluştur</h3>
                <button onclick="closeCreateOrderModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createOrderForm" action="" method="POST" class="flex-1 flex flex-col h-full">
                @csrf
                <input type="hidden" id="createOrderTableId" name="table_id">
                <div class="flex-1 flex flex-col">
                    <!-- Masa Bilgisi -->
                    <div class="p-4 border-b flex flex-col gap-2">
                        <span id="createOrderTableInfo" class="font-medium text-gray-700"></span>
                        <input type="text" id="createOrderCustomerName" name="customer_name" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-[#d4a373] focus:ring focus:ring-[#d4a373] focus:ring-opacity-50" placeholder="Müşteri Adı Soyadı" required>
                    </div>
                    <!-- Ana İçerik: Sol (ürün seçimi) ve Sağ (eklenen ürünler) sütunlar -->
                    <div class="flex-1 flex overflow-hidden">
                        <!-- Sol Sütun: Ürün arama, kategori, ürünler -->
                        <div class="flex-1 flex flex-col border-r min-w-0">
                            <div class="p-4 border-b">
                                <div class="relative">
                                    <input type="text" id="createOrderProductSearch" placeholder="Ürün ara..." class="w-full rounded-md border-gray-300 pl-10 pr-4 py-2 shadow-sm focus:border-[#d4a373] focus:ring focus:ring-[#d4a373] focus:ring-opacity-50" autocomplete="off">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 flex overflow-hidden">
                                <!-- Kategoriler -->
                                <div class="w-1/4 border-r overflow-y-auto">
                                    <div class="p-2">
                                        <button type="button" class="create-order-category-btn w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors" data-category="all">Tüm Ürünler</button>
                                        @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                                        <button type="button" class="create-order-category-btn w-full text-left px-4 py-2 rounded-md mb-1 hover:bg-gray-100 transition-colors" data-category="{{ $category->id }}">{{ $category->name }}</button>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Ürünler -->
                                <div class="flex-1 overflow-y-auto p-4">
                                    <div id="createOrderProductGrid" class="grid grid-cols-2 gap-4">
                                        @foreach(\App\Models\Product::with(['category', 'stock'])->orderBy('name')->get() as $product)
                                        <div class="create-order-product-item border rounded-lg p-3 transition-colors @if($product->stock && $product->stock->quantity == 0) bg-red-50 border-red-200 opacity-70 cursor-not-allowed pointer-events-none @else cursor-pointer hover:border-[#d4a373] @endif"
                                             data-id="{{ $product->id }}"
                                             data-name="{{ $product->name }}"
                                             data-price="{{ number_format($product->current_price, 2) }}"
                                             data-stock="{{ $product->stock->quantity ?? 0 }}"
                                             data-category="{{ $product->category_id }}">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                                    <p class="text-sm text-gray-500">{{ number_format($product->current_price, 2) }}₺</p>
                                                </div>
                                                <span class="text-sm px-2 py-1 rounded-full @if($product->stock && $product->stock->quantity > 0) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                    Stok: {{ $product->stock->quantity ?? 0 }}
                                                </span>
                                            </div>
                                            @if($product->stock && $product->stock->quantity == 0)
                                                <div class="mt-3 flex items-center justify-center">
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-md border border-red-200 bg-red-50 text-red-600 text-sm font-semibold">
                                                        <i class="fa-solid fa-circle-exclamation text-red-400"></i>
                                                        Stokta Yok
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!-- Seçili Ürün ve Miktar Ekleme -->
                            <div class="border-t bg-gray-50 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div id="createOrderSelectedProductInfo" class="text-gray-500">Henüz ürün seçilmedi</div>
                                    <div id="createOrderQuantitySection" class="hidden items-center space-x-2">
                                        <button type="button" onclick="decrementCreateOrderQuantity()" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200"><i class="fas fa-minus"></i></button>
                                        <input type="number" id="createOrderQuantity" min="1" value="1" class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-[#d4a373] focus:ring focus:ring-[#d4a373] focus:ring-opacity-50">
                                        <button type="button" onclick="incrementCreateOrderQuantity()" class="p-2 rounded-md bg-gray-100 hover:bg-gray-200"><i class="fas fa-plus"></i></button>
                                    </div>
                                    <button type="button" id="createOrderAddProductBtn" class="ml-4 px-4 py-2 bg-[#d4a373] border border-transparent rounded-md text-sm font-medium text-white hover:bg-[#b58863] disabled:opacity-50 disabled:cursor-not-allowed" disabled>Listeye Ekle</button>
                                </div>
                            </div>
                        </div>
                        <!-- Sağ Sütun: Eklenen ürünler ve butonlar -->
                        <div class="w-80 flex flex-col bg-gray-50 p-4">
                            <div class="font-semibold text-[#d4a373] mb-2">Eklenen Ürünler</div>
                            <div id="createOrderProductList" class="flex-1 mb-4 max-h-96 overflow-y-auto"></div>
                            <div class="flex flex-col gap-2 mt-auto">
                                <button type="submit" id="createOrderSubmitBtn" class="px-4 py-2 bg-[#d4a373] border border-transparent rounded-md text-sm font-medium text-white hover:bg-[#b58863] disabled:opacity-50 disabled:cursor-not-allowed" disabled>Siparişi Oluştur</button>
                                <button type="button" onclick="closeCreateOrderModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">İptal</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // CSRF token ayarla
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Aktif görünümü sakla
        function saveActiveView(view) {
            sessionStorage.setItem('activeView', view);
        }

        // Görünüm değiştirme
        function toggleView(view) {
            // Tüm bölümleri gizle
            document.getElementById('tablesSection').style.display = 'none';
            document.getElementById('activeOrdersSection').style.display = 'none';
            document.getElementById('deliveredOrdersSection').style.display = 'none';

            // Seçilen bölümü göster
            if (view === 'tables') {
                document.getElementById('tablesSection').style.display = 'block';
            } else if (view === 'active') {
                document.getElementById('activeOrdersSection').style.display = 'block';
            } else if (view === 'delivered') {
                document.getElementById('deliveredOrdersSection').style.display = 'block';
            }

            // Butonların aktif durumunu güncelle
            const buttons = document.querySelectorAll('button[onclick^="toggleView"]');
            buttons.forEach(button => {
                if (button.getAttribute('onclick').includes(view)) {
                    button.classList.add('text-[#d4a373]');
                    button.classList.remove('text-gray-600');
                } else {
                    button.classList.remove('text-[#d4a373]');
                    button.classList.add('text-gray-600');
                }
            });

            // Aktif görünümü kaydet
            saveActiveView(view);
        }

        // Sayfa yüklendiğinde çalışacak ana fonksiyon
        document.addEventListener('DOMContentLoaded', function() {
            // Teslim etme formları için onay penceresi
            const deliveryForms = document.querySelectorAll('.delivery-form');
            deliveryForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Siparişi teslim etmek istediğinize emin misiniz?',
                        text: "Bu işlem geri alınamaz!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Evet, Teslim Edildi',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Son aktif görünümü geri yükle veya varsayılan görünümü göster
            const lastActiveView = sessionStorage.getItem('activeView');
            if (lastActiveView) {
                toggleView(lastActiveView);
            } else {
                // İlk kez yükleniyorsa aktif siparişleri göster
                toggleView('active');
            }

            // Ürün arama ve seçme işlemleri
            const productSearch = document.getElementById('productSearch');
            const productGrid = document.getElementById('productGrid');
            const productItems = document.querySelectorAll('.product-item');
            const quantitySection = document.getElementById('quantitySection');
            const selectedProductInput = document.getElementById('selectedProduct');
            const selectedProductInfo = document.getElementById('selectedProductInfo');
            const submitButton = document.getElementById('submitButton');
            const categoryButtons = document.querySelectorAll('.category-btn');

            // Kategori filtreleme
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-category');
                    
                    // Aktif kategori vurgusunu güncelle
                    categoryButtons.forEach(btn => btn.classList.remove('bg-[#f5e6d3]', 'text-[#d4a373]'));
                    this.classList.add('bg-[#f5e6d3]', 'text-[#d4a373]');
                    
                    // Ürünleri filtrele
                    productItems.forEach(item => {
                        if (categoryId === 'all' || item.getAttribute('data-category') === categoryId) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Ürün arama
            if (productSearch) {
                productSearch.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    
                    productItems.forEach(item => {
                        const productName = item.getAttribute('data-name').toLowerCase();
                        
                        if (productName.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // Ürün seçme işlemi
            if (productItems) {
                productItems.forEach(item => {
                    item.addEventListener('click', function() {
                        selectProduct(this);
                    });
                });
            }
        });

        // Ürün seçme fonksiyonu
        function selectProduct(element) {
            const productItems = document.querySelectorAll('.product-item');
            const quantitySection = document.getElementById('quantitySection');
            const selectedProductInput = document.getElementById('selectedProduct');
            const selectedProductInfo = document.getElementById('selectedProductInfo');
            const submitButton = document.getElementById('submitButton');

            // Önceki seçili ürünün vurgusunu kaldır
            productItems.forEach(item => item.classList.remove('border-[#d4a373]', 'bg-[#f5e6d3]'));
            
            // Yeni seçilen ürünü vurgula
            element.classList.add('border-[#d4a373]', 'bg-[#f5e6d3]');
            
            // Seçilen ürün bilgilerini güncelle
            selectedProductInput.value = element.getAttribute('data-id');
            selectedProductInfo.textContent = `${element.getAttribute('data-name')} - ${element.getAttribute('data-price')}₺`;
            
            // Miktar bölümünü göster
            quantitySection.classList.remove('hidden');
            quantitySection.classList.add('flex');
            
            // Submit butonunu aktif et
            submitButton.disabled = false;
        }

        function incrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }

        function decrementQuantity() {
            const quantityInput = document.getElementById('quantity');
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }

        function closeAddProductModal() {
            const modal = document.getElementById('addProductModal');
            const productSearch = document.getElementById('productSearch');
            const quantitySection = document.getElementById('quantitySection');
            const submitButton = document.getElementById('submitButton');
            const productItems = document.querySelectorAll('.product-item');
            
            // Modalı gizle
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            
            // Form içeriğini sıfırla
            document.getElementById('addProductForm').reset();
            productSearch.value = '';
            quantitySection.classList.add('hidden');
            submitButton.disabled = true;
            
            // Ürün seçimini temizle
            productItems.forEach(item => item.classList.remove('border-[#d4a373]', 'bg-[#f5e6d3]'));
            
            // Tüm ürünleri görünür yap
            productItems.forEach(item => {
                item.style.display = 'block';
                item.parentElement.style.display = 'block';
            });
        }

        // Modal işlemleri
        function openAddProductModal(orderId) {
            const modal = document.getElementById('addProductModal');
            const form = document.getElementById('addProductForm');
            
            // Form action URL'sini ayarla
            form.action = `/waiter/orders/${orderId}/add-product`;
            
            // Modalı göster
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Modal dışına tıklandığında kapanmasını engelle
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAddProductModal();
                }
            });
        }

        // Form submit işlemi
        document.getElementById('addProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Form verilerini al
            const formData = new FormData(this);
            
            // AJAX isteği gönder
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Başarılı mesajı göster
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Ürün siparişe eklendi.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Sayfayı yenile
            location.reload();
                    });
                } else {
                    // Hata mesajı göster
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: data.message || 'Bir hata oluştu.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu.'
                });
            })
            .finally(() => {
                // Modalı kapat
                closeAddProductModal();
            });
        });

        // Sipariş Oluşturma Modalı Aç/Kapat
        function openCreateOrderModal(tableId) {
            const modal = document.getElementById('createOrderModal');
            const tableInfo = document.getElementById('createOrderTableInfo');
            const tableIdInput = document.getElementById('createOrderTableId');
            // Masa bilgisi göster
            const tableCard = document.querySelector(`[onclick*='openCreateOrderModal(${tableId})']`).closest('.bg-white');
            const masaNo = tableCard.querySelector('h3').textContent;
            const kapasite = tableCard.querySelector('p').textContent;
            tableInfo.textContent = `${masaNo} - ${kapasite}`;
            tableIdInput.value = tableId;
            // Modalı göster
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Temizle
            resetCreateOrderModal();
        }
        function closeCreateOrderModal() {
            const modal = document.getElementById('createOrderModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        function resetCreateOrderModal() {
            document.getElementById('createOrderProductSearch').value = '';
            document.getElementById('createOrderSelectedProductInfo').textContent = 'Henüz ürün seçilmedi';
            document.getElementById('createOrderQuantitySection').classList.add('hidden');
            document.getElementById('createOrderAddProductBtn').disabled = true;
            document.getElementById('createOrderQuantity').value = 1;
            document.getElementById('createOrderProductList').innerHTML = '';
            createOrderSelectedProduct = null;
            createOrderProductList = [];
            document.getElementById('createOrderSubmitBtn').disabled = true;
            // Tüm ürünleri göster
            document.querySelectorAll('.create-order-product-item').forEach(item => {
                item.style.display = 'block';
                item.classList.remove('border-[#d4a373]', 'bg-[#f5e6d3]');
            });
        }
        // Ürün seçme, arama, kategori filtreleme, miktar ayarlama, ekleme ve listeleme işlemleri için temel JS değişkenleri ve eventler
        let createOrderSelectedProduct = null;
        let createOrderProductList = [];
        document.querySelectorAll('.create-order-product-item').forEach(item => {
            // Sadece stok varsa tıklanabilir olsun
            if (parseInt(item.getAttribute('data-stock')) > 0) {
                item.addEventListener('click', function() {
                    createOrderSelectedProduct = {
                        id: this.getAttribute('data-id'),
                        name: this.getAttribute('data-name'),
                        price: this.getAttribute('data-price'),
                        stock: this.getAttribute('data-stock')
                    };
                    document.getElementById('createOrderSelectedProductInfo').textContent = `${createOrderSelectedProduct.name} - ${createOrderSelectedProduct.price}₺`;
                    document.getElementById('createOrderQuantitySection').classList.remove('hidden');
                    document.getElementById('createOrderAddProductBtn').disabled = false;
                });
            }
        });
        document.getElementById('createOrderAddProductBtn').addEventListener('click', function() {
            if (!createOrderSelectedProduct) return;
            const quantity = parseInt(document.getElementById('createOrderQuantity').value);
            if (quantity < 1) return;
            // Listeye ekle
            createOrderProductList.push({ ...createOrderSelectedProduct, quantity });
            renderCreateOrderProductList();
            // Temizle
            createOrderSelectedProduct = null;
            document.getElementById('createOrderSelectedProductInfo').textContent = 'Henüz ürün seçilmedi';
            document.getElementById('createOrderQuantitySection').classList.add('hidden');
            document.getElementById('createOrderAddProductBtn').disabled = true;
            document.getElementById('createOrderQuantity').value = 1;
        });
        function renderCreateOrderProductList() {
            const listDiv = document.getElementById('createOrderProductList');
            listDiv.innerHTML = '';
            if (createOrderProductList.length === 0) {
                document.getElementById('createOrderSubmitBtn').disabled = true;
                return;
            }
            createOrderProductList.forEach((item, idx) => {
                const el = document.createElement('div');
                el.className = 'flex items-center justify-between bg-white border rounded p-2 mb-2';
                el.innerHTML = `<span>${item.name} <span class='text-xs text-gray-500'>x${item.quantity}</span></span><button type='button' onclick='removeCreateOrderProduct(${idx})' class='text-red-500 hover:text-red-700 text-xs ml-2'>Kaldır</button>`;
                listDiv.appendChild(el);
            });
            document.getElementById('createOrderSubmitBtn').disabled = false;
        }
        function removeCreateOrderProduct(idx) {
            createOrderProductList.splice(idx, 1);
            renderCreateOrderProductList();
        }
        function incrementCreateOrderQuantity() {
            const quantityInput = document.getElementById('createOrderQuantity');
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
        function decrementCreateOrderQuantity() {
            const quantityInput = document.getElementById('createOrderQuantity');
            if (parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }
        // Kategori filtreleme
        const createOrderCategoryButtons = document.querySelectorAll('.create-order-category-btn');
        const createOrderProductItems = document.querySelectorAll('.create-order-product-item');
        createOrderCategoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category');
                createOrderCategoryButtons.forEach(btn => btn.classList.remove('bg-[#f5e6d3]', 'text-[#d4a373]'));
                this.classList.add('bg-[#f5e6d3]', 'text-[#d4a373]');
                createOrderProductItems.forEach(item => {
                    if (categoryId === 'all' || item.getAttribute('data-category') === categoryId) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
        // Ürün arama
        const createOrderProductSearch = document.getElementById('createOrderProductSearch');
        if (createOrderProductSearch) {
            createOrderProductSearch.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                createOrderProductItems.forEach(item => {
                    const productName = item.getAttribute('data-name').toLowerCase();
                    if (productName.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        // Form submit işlemi
        document.getElementById('createOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const tableId = document.getElementById('createOrderTableId').value;
            const customerName = document.getElementById('createOrderCustomerName').value;
            const products = createOrderProductList.map(item => ({ id: item.id, quantity: item.quantity }));
            if (!tableId || !customerName || products.length === 0) return;
            fetch('/waiter/orders/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    table_id: tableId,
                    customer_name: customerName,
                    products: products
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: 'Sipariş oluşturuldu.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: data.message || 'Bir hata oluştu.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Bir hata oluştu.'
                });
            })
            .finally(() => {
                closeCreateOrderModal();
            });
        });
    </script>
</body>
</html>