<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Ürün Yönetimi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Modal select elements için scroll özelliği */
        .modal-select select, #productCategory, #productManufacturer {
            max-height: 200px;
            overflow-y: auto !important;
        }
        
        /* Webkit (Chrome, Safari, Edge) için scrollbar stilini özelleştirme */
        .modal-select select::-webkit-scrollbar,
        #productCategory::-webkit-scrollbar,
        #productManufacturer::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-select select::-webkit-scrollbar-track,
        #productCategory::-webkit-scrollbar-track,
        #productManufacturer::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .modal-select select::-webkit-scrollbar-thumb,
        #productCategory::-webkit-scrollbar-thumb,
        #productManufacturer::-webkit-scrollbar-thumb {
            background: #d4a373;
            border-radius: 4px;
        }
        
        .modal-select select::-webkit-scrollbar-thumb:hover,
        #productCategory::-webkit-scrollbar-thumb:hover,
        #productManufacturer::-webkit-scrollbar-thumb:hover {
            background: #c48c63;
        }
        
        /* Firefox için scrollbar stilini özelleştirme */
        .modal-select select,
        #productCategory,
        #productManufacturer {
            scrollbar-width: thin;
            scrollbar-color: #d4a373 #f1f1f1;
        }
        
        /* Select option'lar için stil */
        .modal-select select option,
        #productCategory option,
        #productManufacturer option {
            padding: 8px 12px;
        }
        
        .modal-select select option:hover,
        #productCategory option:hover,
        #productManufacturer option:hover {
            background-color: #f8f9fa;
        }

        /* Custom dropdown styles */
        .custom-select-wrapper {
            position: relative;
            user-select: none;
        }

        .custom-select-trigger {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
            font-size: 0.875rem;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            cursor: pointer;
            min-height: 42px;
        }

        .custom-select-trigger:hover {
            border-color: #d4a373;
        }

        .custom-select-trigger i {
            transition: transform 0.2s ease;
        }

        .custom-select-wrapper.open .custom-select-trigger i {
            transform: rotate(180deg);
        }

        .custom-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            margin-top: 0.25rem;
            max-height: 200px;
            overflow-y: auto;
            z-index: 40;
            display: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .custom-select-wrapper.open .custom-options {
            display: block;
        }

        .custom-option {
            padding: 0.625rem 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #fff;
        }

        .custom-option:hover {
            background-color: #f8f9fa;
        }

        .custom-option.selected {
            background-color: #fff;
            color: #d4a373;
            font-weight: 500;
        }

        /* Scrollbar styles for custom options */
        .custom-options::-webkit-scrollbar {
            width: 6px;
        }

        .custom-options::-webkit-scrollbar-track {
            background: #fff;
            border-radius: 3px;
        }

        .custom-options::-webkit-scrollbar-thumb {
            background: #d4a373;
            border-radius: 3px;
        }

        .custom-options::-webkit-scrollbar-thumb:hover {
            background: #c48c63;
        }
    </style>
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
            <h1 class="text-2xl font-bold">Ürün Yönetimi</h1>
            <div class="flex items-center gap-4">
                <button onclick="openManufacturersModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors">
                    <i class="fas fa-industry mr-2"></i>Üreticileri Yönet
                </button>
                <button onclick="openAddProductModal()" class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                    <i class="fas fa-plus mr-2"></i>Yeni Ürün Ekle
                </button>
            </div>
        </div>

        <!-- Filtreleme ve Arama -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <div class="custom-select-wrapper" id="categoryWrapper">
                        <div class="custom-select-trigger">
                            <span id="selectedCategory">Tüm Kategoriler</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                        <div class="custom-options">
                            <div class="custom-option selected" data-value="">Tüm Kategoriler</div>
                            @foreach($categories as $category)
                                <div class="custom-option" data-value="{{ $category->id }}">{{ $category->name }}</div>
                            @endforeach
                        </div>
                        <input type="hidden" id="filterCategory" name="category" value="">
                    </div>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Üretici</label>
                    <div class="custom-select-wrapper" id="manufacturerWrapper">
                        <div class="custom-select-trigger">
                            <span id="selectedManufacturer">Tüm Üreticiler</span>
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                        <div class="custom-options">
                            <div class="custom-option selected" data-value="">Tüm Üreticiler</div>
                            @foreach($manufacturers as $manufacturer)
                                <div class="custom-option" data-value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</div>
                            @endforeach
                        </div>
                        <input type="hidden" id="filterManufacturer" name="manufacturer" value="">
                    </div>
                </div>
                <div class="flex-1 min-w-[250px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fiyat Aralığı</label>
                    <div class="flex space-x-2">
                        <input type="number" id="minPrice" placeholder="Min" class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        <input type="number" id="maxPrice" placeholder="Max" class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                    </div>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arama</label>
                    <div class="relative">
                        <input type="text" id="searchQuery" placeholder="Ürün ara..." class="w-full border border-gray-300 rounded-md p-2 pl-10 focus:ring-[#d4a373] focus:border-[#d4a373]">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="clearFilters()" class="h-10 bg-gray-500 text-white px-4 rounded hover:bg-gray-600 transition-colors flex items-center">
                        <i class="fas fa-times mr-2"></i>Temizle
                    </button>
                    <button onclick="applyFilters()" class="h-10 bg-[#d4a373] text-white px-4 rounded hover:bg-[#c48c63] transition-colors flex items-center">
                        <i class="fas fa-filter mr-2"></i>Filtrele
                    </button>
                </div>
            </div>
        </div>

        <!-- Ürün Tablosu -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Üretici</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satış Fiyatı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($product->image)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-utensils text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->description }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->category->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($product->manufacturer)
                                    {{ $product->manufacturer->name }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($product->latestStock && $product->latestStock->sale_price)
                                    ₺{{ number_format($product->latestStock->sale_price, 2) }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($product->currentStock)
                                    {{ $product->currentStock->quantity }} {{ $product->currentStock->unit }}
                                @else
                                    0
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->currentStock && $product->currentStock->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->currentStock && $product->currentStock->quantity > 0 ? 'Aktif' : 'Stokta Yok' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="editProduct({{ $product->id }})" class="text-[#d4a373] hover:text-[#c48c63] mr-3">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sayfalama -->
        <div class="mt-4">
            {{ $products->links() }}
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
        function openAddProductModal() {
            Swal.fire({
                title: 'Yeni Ürün Ekle',
                html: `
                    <form id="addProductForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı</label>
                            <input type="text" id="productName" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Barkod</label>
                            <input type="text" id="productBarcode" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <div class="custom-select-wrapper" id="addProductCategoryWrapper">
                                <div class="custom-select-trigger">
                                    <span>Seçiniz...</span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                                <div class="custom-options">
                                    <div class="custom-option selected" data-value="">Kategori Seçin</div>
                                    @foreach($categories as $category)
                                        <div class="custom-option" data-value="{{ $category->id }}">{{ $category->name }}</div>
                                    @endforeach
                                </div>
                                <input type="hidden" id="productCategory" name="category" value="">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Üretici</label>
                            <div class="custom-select-wrapper" id="addProductManufacturerWrapper">
                                <div class="custom-select-trigger">
                                    <span>Seçiniz...</span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                                <div class="custom-options">
                                    <div class="custom-option selected" data-value="">Yükleniyor...</div>
                                </div>
                                <input type="hidden" id="productManufacturer" name="manufacturer" value="">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                            <textarea id="productDescription" class="w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Kaydet',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#6b7280',
                didOpen: () => {
                    // Üreticileri dinamik olarak yükle
                    const manufacturerWrapper = document.getElementById('addProductManufacturerWrapper');
                    fetch('/admin/manufacturers', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const optionsContainer = manufacturerWrapper.querySelector('.custom-options');
                            optionsContainer.innerHTML = '<div class="custom-option selected" data-value="">Üretici Seçin</div>';
                            data.manufacturers.forEach(manufacturer => {
                                if (manufacturer.is_active) {
                                    const option = document.createElement('div');
                                    option.className = 'custom-option';
                                    option.dataset.value = manufacturer.id;
                                    option.textContent = manufacturer.name;
                                    optionsContainer.appendChild(option);
                                }
                            });
                            
                            // Custom dropdown işlevselliğini ekle
                            initializeCustomDropdown(manufacturerWrapper);
                        } else {
                            manufacturerWrapper.querySelector('.custom-options').innerHTML = '<div class="custom-option selected" data-value="">Üretici bulunamadı</div>';
                        }
                    })
                    .catch(() => {
                        manufacturerWrapper.querySelector('.custom-options').innerHTML = '<div class="custom-option selected" data-value="">Üretici yüklenemedi</div>';
                    });

                    // Kategori dropdown'ını başlat
                    initializeCustomDropdown(document.getElementById('addProductCategoryWrapper'));
                },
                preConfirm: () => {
                    const formData = new FormData();
                    formData.append('name', document.getElementById('productName').value);
                    formData.append('barcode', document.getElementById('productBarcode').value);
                    formData.append('category_id', document.getElementById('productCategory').value);
                    formData.append('manufacturer_id', document.getElementById('productManufacturer').value);
                    formData.append('description', document.getElementById('productDescription').value);

                    return fetch('{{ route("admin.products.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
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
                        text: result.value.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        function editProduct(productId) {
            fetch(`/admin/products/${productId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Sunucu hatası oluştu');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const product = data.product;
                    Swal.fire({
                        title: 'Ürün Düzenle',
                        html: `
                            <form id="editProductForm" class="text-left">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı</label>
                                    <input type="text" id="editProductName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${product.name}">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Barkod</label>
                                    <input type="text" id="editProductBarcode" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${product.barcode || ''}">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <div class="custom-select-wrapper" id="editProductCategoryWrapper">
                                        <div class="custom-select-trigger">
                                            <span>Seçiniz...</span>
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                        <div class="custom-options">
                                            @foreach($categories as $category)
                                                <div class="custom-option" data-value="{{ $category->id }}">{{ $category->name }}</div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" id="editProductCategory" name="category" value="">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Üretici</label>
                                    <div class="custom-select-wrapper" id="editProductManufacturerWrapper">
                                        <div class="custom-select-trigger">
                                            <span>Yükleniyor...</span>
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                        <div class="custom-options">
                                            <div class="custom-option selected" data-value="">Yükleniyor...</div>
                                        </div>
                                        <input type="hidden" id="editProductManufacturer" name="manufacturer" value="">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                                    <textarea id="editProductDescription" class="w-full px-3 py-2 border border-gray-300 rounded-md">${product.description || ''}</textarea>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Güncelle',
                        cancelButtonText: 'İptal',
                        confirmButtonColor: '#d4a373',
                        cancelButtonColor: '#6b7280',
                        didOpen: () => {
                            // Kategori seçili gelsin
                            const categoryWrapper = document.getElementById('editProductCategoryWrapper');
                            const categoryOptions = categoryWrapper.querySelectorAll('.custom-option');
                            categoryOptions.forEach(option => {
                                if (option.dataset.value == product.category_id) {
                                    categoryOptions.forEach(opt => opt.classList.remove('selected'));
                                    option.classList.add('selected');
                                    categoryWrapper.querySelector('input[type="hidden"]').value = product.category_id;
                                    categoryWrapper.querySelector('.custom-select-trigger span').textContent = option.textContent;
                                }
                            });

                            // Üreticileri dinamik yükle ve seçili yap
                            const manufacturerWrapper = document.getElementById('editProductManufacturerWrapper');
                            fetch('/admin/manufacturers', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const optionsContainer = manufacturerWrapper.querySelector('.custom-options');
                                    optionsContainer.innerHTML = '<div class="custom-option" data-value="">Üretici Seçin</div>';
                                    data.manufacturers.forEach(manufacturer => {
                                        if (manufacturer.is_active) {
                                            const option = document.createElement('div');
                                            option.className = 'custom-option';
                                            option.dataset.value = manufacturer.id;
                                            option.textContent = manufacturer.name;
                                            if (product.manufacturer_id == manufacturer.id) {
                                                option.classList.add('selected');
                                                manufacturerWrapper.querySelector('.custom-select-trigger span').textContent = manufacturer.name;
                                                manufacturerWrapper.querySelector('input[type="hidden"]').value = manufacturer.id;
                                            }
                                            optionsContainer.appendChild(option);
                                        }
                                    });
                                    
                                    // Custom dropdown işlevselliğini ekle
                                    initializeCustomDropdown(manufacturerWrapper);
                                } else {
                                    manufacturerWrapper.querySelector('.custom-options').innerHTML = '<div class="custom-option selected" data-value="">Üretici bulunamadı</div>';
                                }
                            })
                            .catch(() => {
                                manufacturerWrapper.querySelector('.custom-options').innerHTML = '<div class="custom-option selected" data-value="">Üretici yüklenemedi</div>';
                            });

                            // Kategori dropdown'ını başlat
                            initializeCustomDropdown(categoryWrapper);
                        },
                        preConfirm: () => {
                            const name = document.getElementById('editProductName').value;
                            const barcode = document.getElementById('editProductBarcode').value;
                            const category_id = document.getElementById('editProductCategory').value;
                            const manufacturer_id = document.getElementById('editProductManufacturer').value;
                            const description = document.getElementById('editProductDescription').value;

                            if (!name) {
                                Swal.showValidationMessage('Ürün adı boş bırakılamaz');
                                return false;
                            }
                            if (!category_id) {
                                Swal.showValidationMessage('Lütfen bir kategori seçin');
                                return false;
                            }
                            const formData = new FormData();
                            formData.append('name', name);
                            formData.append('barcode', barcode);
                            formData.append('category_id', category_id);
                            formData.append('manufacturer_id', manufacturer_id);
                            formData.append('description', description);
                            formData.append('_method', 'PUT');
                            return fetch(`/admin/products/${productId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Sunucu hatası oluştu');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message || 'Ürün güncellenirken bir hata oluştu');
                                }
                                return data;
                            })
                            .catch(error => {
                                console.error('Hata:', error);
                                throw new Error(error.message || 'Ürün güncellenirken bir hata oluştu');
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: result.value.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                } else {
                    throw new Error(data.message || 'Ürün bilgileri alınırken bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                Swal.fire('Hata!', error.message || 'Ürün bilgileri alınırken bir hata oluştu', 'error');
            });
        }

        function deleteProduct(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu ürünü silmek istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                preConfirm: () => {
                    return fetch(`/admin/products/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
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
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: result.value.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        // Custom dropdown başlatma fonksiyonu
        function initializeCustomDropdown(wrapper) {
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const options = wrapper.querySelector('.custom-options');
            const optionItems = wrapper.querySelectorAll('.custom-option');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const selectedText = wrapper.querySelector('.custom-select-trigger span');

            // Trigger tıklama olayı
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                
                // Diğer açık dropdownları kapat
                document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                    if (w !== wrapper) {
                        w.classList.remove('open');
                    }
                });
                
                wrapper.classList.toggle('open');
            });

            // Seçenek tıklama olayı
            optionItems.forEach(option => {
                option.addEventListener('click', () => {
                    // Seçili sınıfını güncelle
                    optionItems.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');

                    // Hidden input ve görünen metni güncelle
                    hiddenInput.value = option.dataset.value;
                    selectedText.textContent = option.textContent;

                    // Dropdown'ı kapat
                    wrapper.classList.remove('open');
                });
            });
        }

        // Sayfa yüklendiğinde tüm custom dropdown'ları başlat
        document.addEventListener('DOMContentLoaded', function() {
            // Ana sayfadaki dropdown'lar için
            document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
                initializeCustomDropdown(wrapper);
            });

            // Sayfa herhangi bir yerine tıklandığında dropdownları kapat
            document.addEventListener('click', () => {
                document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => wrapper.classList.remove('open'));
            });
        });

        // Filtreleme fonksiyonu
        function applyFilters() {
            const category = document.getElementById('filterCategory').value;
            const manufacturer = document.getElementById('filterManufacturer').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            const searchQuery = document.getElementById('searchQuery').value;

            const queryParams = new URLSearchParams();
            if (category) queryParams.append('category', category);
            if (manufacturer) queryParams.append('manufacturer', manufacturer);
            if (minPrice) queryParams.append('min_price', minPrice);
            if (maxPrice) queryParams.append('max_price', maxPrice);
            if (searchQuery) queryParams.append('search', searchQuery);

            window.location.href = `${window.location.pathname}?${queryParams.toString()}`;
        }

        // Filtreleri temizleme fonksiyonu
        function clearFilters() {
            // Kategori dropdown'ını sıfırla
            const categoryWrapper = document.getElementById('categoryWrapper');
            categoryWrapper.querySelector('.custom-select-trigger span').textContent = 'Tüm Kategoriler';
            categoryWrapper.querySelector('input[type="hidden"]').value = '';
            categoryWrapper.querySelectorAll('.custom-option').forEach(opt => {
                opt.classList.remove('selected');
                if (opt.dataset.value === '') opt.classList.add('selected');
            });

            // Üretici dropdown'ını sıfırla
            const manufacturerWrapper = document.getElementById('manufacturerWrapper');
            manufacturerWrapper.querySelector('.custom-select-trigger span').textContent = 'Tüm Üreticiler';
            manufacturerWrapper.querySelector('input[type="hidden"]').value = '';
            manufacturerWrapper.querySelectorAll('.custom-option').forEach(opt => {
                opt.classList.remove('selected');
                if (opt.dataset.value === '') opt.classList.add('selected');
            });

            // Diğer filtreleri sıfırla
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            document.getElementById('searchQuery').value = '';

            // Sayfayı filtresiz haliyle yeniden yükle
            window.location.href = window.location.pathname;
        }

        // URL'den filtreleri yükleme
        window.addEventListener('load', () => {
            const params = new URLSearchParams(window.location.search);
            
            if (params.has('category')) {
                const categoryValue = params.get('category');
                const categoryWrapper = document.getElementById('categoryWrapper');
                const categoryOption = categoryWrapper.querySelector(`.custom-option[data-value="${categoryValue}"]`);
                if (categoryOption) {
                    categoryWrapper.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('selected'));
                    categoryOption.classList.add('selected');
                    categoryWrapper.querySelector('input[type="hidden"]').value = categoryValue;
                    categoryWrapper.querySelector('.custom-select-trigger span').textContent = categoryOption.textContent;
                }
            }
            
            if (params.has('manufacturer')) {
                const manufacturerValue = params.get('manufacturer');
                const manufacturerWrapper = document.getElementById('manufacturerWrapper');
                const manufacturerOption = manufacturerWrapper.querySelector(`.custom-option[data-value="${manufacturerValue}"]`);
                if (manufacturerOption) {
                    manufacturerWrapper.querySelectorAll('.custom-option').forEach(opt => opt.classList.remove('selected'));
                    manufacturerOption.classList.add('selected');
                    manufacturerWrapper.querySelector('input[type="hidden"]').value = manufacturerValue;
                    manufacturerWrapper.querySelector('.custom-select-trigger span').textContent = manufacturerOption.textContent;
                }
            }
            
            if (params.has('min_price')) {
                document.getElementById('minPrice').value = params.get('min_price');
            }
            if (params.has('max_price')) {
                document.getElementById('maxPrice').value = params.get('max_price');
            }
            if (params.has('search')) {
                document.getElementById('searchQuery').value = params.get('search');
            }
        });

        // --- Üretici Yönetimi Kodları Başlangıç ---
        // inventory.blade.php'den alınan fonksiyonlar aşağıya ekleniyor

        function openManufacturersModal() {
            fetch('/admin/manufacturers')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const manufacturers = data.manufacturers;
                        Swal.fire({
                            title: 'Üreticiler',
                            width: '800px',
                            html: `
                                <div class="h-[600px] flex flex-col">
                                    <div class="mb-4 flex justify-between items-center px-4">
                                        <div class="relative w-64">
                                            <input type="text" id="manufacturerSearch" placeholder="Üretici ara..." 
                                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#d4a373] focus:border-[#d4a373]">
                                            <div class="absolute left-3 top-2.5 text-gray-400">
                                                <i class="fas fa-search"></i>
                                            </div>
                                        </div>
                                        <button type="button" onclick="openNewManufacturerForm()" class="px-4 py-2 bg-[#d4a373] text-white rounded-md hover:bg-[#c48c63] transition-colors">
                                            <i class="fas fa-plus mr-2"></i>Yeni Üretici
                                        </button>
                                    </div>
                                    <div class="flex-1 overflow-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortManufacturers('name')">
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
                                            <tbody id="manufacturersTableBody" class="bg-white divide-y divide-gray-200">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-4 flex justify-between items-center px-4 py-2 border-t">
                                        <div class="text-sm text-gray-500">
                                            Toplam <span id="totalManufacturers">0</span> üretici
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button id="prevPage" class="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span id="pageInfo" class="text-sm">Sayfa <span id="currentPage">1</span>/<span id="totalPages">1</span></span>
                                            <button id="nextPage" class="px-3 py-1 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `,
                            showConfirmButton: false,
                            showCloseButton: true,
                            didOpen: () => {
                                window.manufacturersState = {
                                    manufacturers: manufacturers,
                                    filteredManufacturers: manufacturers,
                                    currentPage: 1,
                                    itemsPerPage: 8,
                                    sortField: 'name',
                                    sortDirection: 'asc'
                                };
                                const searchInput = document.getElementById('manufacturerSearch');
                                searchInput.addEventListener('input', (e) => {
                                    const searchTerm = e.target.value.toLowerCase();
                                    window.manufacturersState.filteredManufacturers = window.manufacturersState.manufacturers.filter(m => 
                                        m.name.toLowerCase().includes(searchTerm) ||
                                        (m.contact_person && m.contact_person.toLowerCase().includes(searchTerm)) ||
                                        (m.email && m.email.toLowerCase().includes(searchTerm))
                                    );
                                    window.manufacturersState.currentPage = 1;
                                    updateManufacturersTable();
                                });
                                document.getElementById('prevPage').addEventListener('click', () => {
                                    if (window.manufacturersState.currentPage > 1) {
                                        window.manufacturersState.currentPage--;
                                        updateManufacturersTable();
                                    }
                                });
                                document.getElementById('nextPage').addEventListener('click', () => {
                                    const totalPages = Math.ceil(window.manufacturersState.filteredManufacturers.length / window.manufacturersState.itemsPerPage);
                                    if (window.manufacturersState.currentPage < totalPages) {
                                        window.manufacturersState.currentPage++;
                                        updateManufacturersTable();
                                    }
                                });
                                updateManufacturersTable();
                            }
                        });
                    } else {
                        Swal.fire('Hata!', data.message || 'Üreticiler yüklenirken bir hata oluştu', 'error');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire('Hata!', 'Üreticiler yüklenirken bir hata oluştu', 'error');
                });
        }

        function openNewManufacturerForm() {
            Swal.fire({
                title: 'Yeni Üretici Ekle',
                html: `
                    <form id="addManufacturerForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label>
                            <input type="text" id="manufacturerName" class="w-full px-3 py-2 border border-gray-300 rounded-md" required minlength="2" maxlength="100">
                            <div id="manufacturerNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">İletişim Kişisi</label>
                            <input type="text" id="manufacturerContact" class="w-full px-3 py-2 border border-gray-300 rounded-md" minlength="2" maxlength="100">
                            <div id="manufacturerContactError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" id="manufacturerPhone" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="5XX XXX XXXX">
                            <div id="manufacturerPhoneError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                            <input type="email" id="manufacturerEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <div id="manufacturerEmailError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                            <textarea id="manufacturerAddress" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="3" maxlength="255"></textarea>
                            <div id="manufacturerAddressError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notlar</label>
                            <textarea id="manufacturerNotes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" maxlength="500"></textarea>
                            <div id="manufacturerNotesError" class="text-red-500 text-xs mt-1 hidden"></div>
                        </div>
                    </form>
                `,
                didOpen: () => {
                    const phoneInput = document.getElementById('manufacturerPhone');
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
                    const form = document.getElementById('addManufacturerForm');
                    const inputs = form.querySelectorAll('input, textarea');
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
                confirmButtonText: 'Ekle',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#6B7280',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const name = document.getElementById('manufacturerName').value;
                    const contact = document.getElementById('manufacturerContact').value;
                    const phone = document.getElementById('manufacturerPhone').value;
                    const email = document.getElementById('manufacturerEmail').value;
                    const address = document.getElementById('manufacturerAddress').value;
                    const notes = document.getElementById('manufacturerNotes').value;
                    let isValid = true;
                    let errorMessage = '';
                    if (!name || name.length < 2) {
                        isValid = false;
                        errorMessage = 'Üretici adı en az 2 karakter olmalıdır';
                        document.getElementById('manufacturerNameError').textContent = errorMessage;
                        document.getElementById('manufacturerNameError').classList.remove('hidden');
                    }
                    if (contact && contact.length < 2) {
                        isValid = false;
                        errorMessage = 'İletişim kişisi adı en az 2 karakter olmalıdır';
                        document.getElementById('manufacturerContactError').textContent = errorMessage;
                        document.getElementById('manufacturerContactError').classList.remove('hidden');
                    }
                    if (phone && !isValidPhone(phone)) {
                        isValid = false;
                        errorMessage = 'Geçerli bir telefon numarası giriniz';
                        document.getElementById('manufacturerPhoneError').textContent = errorMessage;
                        document.getElementById('manufacturerPhoneError').classList.remove('hidden');
                    }
                    if (email && !isValidEmail(email)) {
                        isValid = false;
                        errorMessage = 'Geçerli bir e-posta adresi giriniz';
                        document.getElementById('manufacturerEmailError').textContent = errorMessage;
                        document.getElementById('manufacturerEmailError').classList.remove('hidden');
                    }
                    if (!isValid) {
                        Swal.showValidationMessage(errorMessage);
                        return false;
                    }
                    return fetch('/admin/manufacturers', {
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
                            throw new Error(data.message || 'Üretici eklenirken bir hata oluştu');
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
                    window.manufacturersState.manufacturers.push(result.value.manufacturer);
                    window.manufacturersState.filteredManufacturers = window.manufacturersState.manufacturers;
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Üretici başarıyla eklendi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        openManufacturersModal();
                        updateManufacturersTable();
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    openManufacturersModal();
                }
            });
        }

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

        function updateManufacturersTable() {
            const state = window.manufacturersState;
            const startIndex = (state.currentPage - 1) * state.itemsPerPage;
            const endIndex = startIndex + state.itemsPerPage;
            const sortedManufacturers = [...state.filteredManufacturers].sort((a, b) => {
                const aValue = a[state.sortField] || '';
                const bValue = b[state.sortField] || '';
                return state.sortDirection === 'asc' 
                    ? aValue.localeCompare(bValue)
                    : bValue.localeCompare(aValue);
            });
            const pageManufacturers = sortedManufacturers.slice(startIndex, endIndex);
            const tbody = document.getElementById('manufacturersTableBody');
            tbody.innerHTML = pageManufacturers.map(manufacturer => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        ${manufacturer.name}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${manufacturer.contact_person || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${manufacturer.phone || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${manufacturer.email || '-'}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <button onclick="editManufacturer(${manufacturer.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteManufacturer(${manufacturer.id})" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            const totalPages = Math.ceil(state.filteredManufacturers.length / state.itemsPerPage);
            document.getElementById('currentPage').textContent = state.currentPage;
            document.getElementById('totalPages').textContent = totalPages;
            document.getElementById('totalManufacturers').textContent = state.filteredManufacturers.length;
            document.getElementById('prevPage').disabled = state.currentPage === 1;
            document.getElementById('nextPage').disabled = state.currentPage === totalPages;
        }

        function sortManufacturers(field) {
            if (window.manufacturersState.sortField === field) {
                window.manufacturersState.sortDirection = window.manufacturersState.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                window.manufacturersState.sortField = field;
                window.manufacturersState.sortDirection = 'asc';
            }
            updateManufacturersTable();
        }

        function editManufacturer(id) {
            fetch(`/admin/manufacturers/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const manufacturer = data.manufacturer;
                        Swal.fire({
                            title: 'Üretici Düzenle',
                            html: `
                                <form id="editManufacturerForm" class="text-left">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">İsim *</label>
                                        <input type="text" id="manufacturerName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${manufacturer.name}" required minlength="2" maxlength="100">
                                        <div id="manufacturerNameError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">İletişim Kişisi</label>
                                        <input type="text" id="contactPerson" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${manufacturer.contact_person || ''}" minlength="2" maxlength="100">
                                        <div id="contactPersonError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                        <input type="tel" id="phone" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${manufacturer.phone || ''}" placeholder="5XX XXX XXXX">
                                        <div id="phoneError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                        <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${manufacturer.email || ''}">
                                        <div id="emailError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                                        <textarea id="address" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" maxlength="255">${manufacturer.address || ''}</textarea>
                                        <div id="addressError" class="text-red-500 text-xs mt-1 hidden"></div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" id="isActive" class="form-checkbox h-4 w-4 text-[#d4a373]" ${manufacturer.is_active ? 'checked' : ''}>
                                            <span class="ml-2">Aktif</span>
                                        </label>
                                    </div>
                                </form>
                            `,
                            didOpen: () => {
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
                                const form = document.getElementById('editManufacturerForm');
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
                                const name = document.getElementById('manufacturerName').value;
                                const contactPerson = document.getElementById('contactPerson').value;
                                const phone = document.getElementById('phone').value;
                                const email = document.getElementById('email').value;
                                const address = document.getElementById('address').value;
                                const isActive = document.getElementById('isActive').checked;
                                let isValid = true;
                                let errorMessage = '';
                                if (!name || name.length < 2) {
                                    isValid = false;
                                    errorMessage = 'Üretici adı en az 2 karakter olmalıdır';
                                    document.getElementById('manufacturerNameError').textContent = errorMessage;
                                    document.getElementById('manufacturerNameError').classList.remove('hidden');
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
                                return fetch(`/admin/manufacturers/${id}`, {
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
                                        throw new Error(data.message || 'Üretici güncellenirken bir hata oluştu');
                                    }
                                    return data;
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                Swal.fire({
                                    title: 'Başarılı!',
                                    text: 'Üretici başarıyla güncellendi',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    openManufacturersModal();
                                });
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                openManufacturersModal();
                            }
                        });
                    } else {
                        Swal.fire('Hata!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire('Hata!', 'Üretici bilgileri alınırken bir hata oluştu.', 'error');
                });
        }

        function deleteManufacturer(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu üreticiyi silmek istediğinizden emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/manufacturers/${id}`, {
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
                                text: 'Üretici başarıyla silindi',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                openManufacturersModal();
                            });
                        } else {
                            Swal.fire('Hata!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Hata:', error);
                        Swal.fire('Hata!', 'Üretici silinirken bir hata oluştu.', 'error');
                    });
                }
            });
        }
        // --- Üretici Yönetimi Kodları Bitiş ---
    </script>
</body>
</html>
