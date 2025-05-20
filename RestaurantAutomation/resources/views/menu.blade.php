<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Menü</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .menu-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out, padding 0.3s ease-out;
            opacity: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        .menu-content.active {
            max-height: 2000px;
            opacity: 1;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .menu-banner {
            background-image: url('images/bakery2.jpg');
            background-size: cover;
            background-position: center;
            height: 300px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            transition: opacity 0.5s ease-in-out;
        }

        .menu-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .menu-banner-content {
            position: relative;
            z-index: 2;
        }

        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: repeat(1, 1fr);
            }
            .menu-banner {
                height: 200px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        #mobile-menu {
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            opacity: 0;
            visibility: hidden;
        }

        #mobile-menu.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        #mini-cart {
            transition: all 0.3s ease;
        }

        #mini-cart-items {
            scrollbar-width: thin;
            scrollbar-color: #d4a373 #f5e6d3;
        }

        #mini-cart-items::-webkit-scrollbar {
            width: 6px;
        }

        #mini-cart-items::-webkit-scrollbar-track {
            background: #f5e6d3;
        }

        #mini-cart-items::-webkit-scrollbar-thumb {
            background-color: #d4a373;
            border-radius: 3px;
        }

        .mini-cart-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f5e6d3;
        }

        .mini-cart-item:last-child {
            border-bottom: none;
        }

        #mobile-mini-cart {
            transition: all 0.3s ease;
            max-height: 80vh;
            overflow-y: auto;
        }

        #mobile-mini-cart-items {
            scrollbar-width: thin;
            scrollbar-color: #d4a373 #f5e6d3;
        }

        #mobile-mini-cart-items::-webkit-scrollbar {
            width: 6px;
        }

        #mobile-mini-cart-items::-webkit-scrollbar-track {
            background: #f5e6d3;
        }

        #mobile-mini-cart-items::-webkit-scrollbar-thumb {
            background-color: #d4a373;
            border-radius: 3px;
        }

        /* Accordion stilleri */
        .category-header {
            transition: background-color 0.3s ease;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
        }

        .category-header:hover {
            background-color: #f8f4f0;
        }

        .category-icon {
            transition: transform 0.3s ease;
        }

        .category-icon.rotate {
            transform: rotate(180deg);
        }

        .category-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }

        .menu-content .px-6 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .product-card {
            padding: 0.75rem !important;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .product-description {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

<!-- Navbar -->
<nav class="bg-[#f5e6d3] p-4 shadow-md relative">
    <div class="max-w-7xl mx-auto">
        <!-- Desktop Navbar -->
        <div class="flex justify-between items-center">
            <div class="text-2xl font-bold flex items-center gap-1">
                <a href="{{ route('menu') }}" class="flex items-center gap-1">
                    Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk <span class="text-gray-600 text-lg">cafe</span>
                </a>
            </div>

            <div class="flex items-center gap-4 md:hidden">
                <!-- Mobil Sepet Butonu -->
                <button class="text-gray-600 hover:text-[#d4a373] transition-colors relative" id="mobile-cart-button">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span class="cart-count absolute -top-2 -right-2 bg-[#d4a373] text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </button>

                <!-- Hamburger Menu Button -->
                <button id="mobile-menu-button" class="text-gray-600 hover:text-[#d4a373] transition-colors">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Desktop Menu -->
            <ul class="hidden md:flex items-center space-x-6">
                <li><a href="{{ route('menu') }}" class="hover:text-[#d4a373] transition-colors">Menü</a></li>
                @auth
                <li class="relative group" id="user-menu-desktop">
                    <button class="flex items-center gap-2 hover:text-[#d4a373] transition-colors focus:outline-none" id="user-menu-button">
                        <span class="text-gray-800">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name }}</span></span>
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg z-50 py-2 border border-gray-100">
                        <a href="{{ route('account.info') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-[#f8f4f0] transition-colors">
                            <i class="fa-solid fa-user"></i> Hesap Bilgilerim
                        </a>
                        <a href="{{ route('order.page') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-[#f8f4f0] transition-colors">
                            <i class="fa-solid fa-clock-rotate-left"></i> Geçmiş Siparişlerim
                        </a>
                        <a href="{{ route('favorites') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-[#f8f4f0] transition-colors">
                            <i class="fa-solid fa-heart"></i> Favorilerim
                        </a>
                        <a href="{{ route('notifications') }}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-[#f8f4f0] transition-colors">
                            <i class="fa-solid fa-bell"></i> Bildirimlerim
                        </a>
                        <a href="{{route('support')}}" class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:bg-[#f8f4f0] transition-colors">
                            <i class="fa-solid fa-circle-question"></i> Destek / Yardım
                        </a>
                        <form action="{{ route('auth.logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-red-500 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                            </button>
                        </form>
                    </div>
                </li>
                @else
                <li><a href="{{ route('auth.login') }}" class="hover:text-[#d4a373] transition-colors">Giriş Yap</a></li>
                @endauth
                <li class="relative group">
                    <button class="hover:text-[#d4a373] transition-colors relative" id="cart-button">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count absolute -top-2 -right-2 bg-[#d4a373] text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </button>
                    
                    <!-- Mini Sepet -->
                    <div id="mini-cart" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-3">Sepetim</h3>
                            <div id="mini-cart-items" class="max-h-60 overflow-y-auto">
                                <!-- Sepet öğeleri buraya dinamik olarak eklenecek -->
                            </div>
                            <div class="border-t mt-3 pt-3">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="font-semibold">Toplam:</span>
                                    <span id="mini-cart-total" class="text-[#d4a373] font-bold">0.00₺</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <a href="{{ route('cart') }}{{ request()->query('table') ? '?table='.request()->query('table') : '' }}" 
                                       class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors text-center flex-1">
                                        Sepete Git
                                    </a>
                                    <button onclick="clearCart()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden absolute left-0 right-0 top-full bg-[#f5e6d3] shadow-md z-50">
            <div class="px-4 py-3 space-y-4">
                <a href="{{ route('menu') }}" class="block hover:text-[#d4a373] transition-colors">Menü</a>
                @auth
                    <div class="py-2 border-t border-[#e5d5c0]">
                        <span class="block text-gray-800 mb-2">Merhaba, <span class="font-medium text-[#d4a373]">{{ Auth::user()->name }}</span></span>
                        <a href="{{ route('account.info') }}" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-[#f8f4f0] transition-colors text-gray-700">
                            <i class="fa-solid fa-user"></i> Hesap Bilgilerim
                        </a>
                        <a href="{{ route('order.page') }}" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-[#f8f4f0] transition-colors text-gray-700">
                            <i class="fa-solid fa-clock-rotate-left"></i> Geçmiş Siparişlerim
                        </a>
                        <a href="{{ route('favorites') }}" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-[#f8f4f0] transition-colors text-gray-700">
                            <i class="fa-solid fa-heart"></i> Favorilerim
                        </a>
                        <a href="{{ route('notifications') }}" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-[#f8f4f0] transition-colors text-gray-700">
                            <i class="fa-solid fa-bell"></i> Bildirimlerim
                        </a>
                        <a href="{{route('support')}}" class="flex items-center gap-2 py-2 px-2 rounded hover:bg-[#f8f4f0] transition-colors text-gray-700">
                            <i class="fa-solid fa-circle-question"></i> Destek / Yardım
                        </a>
                        <form action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors text-sm flex items-center gap-2 w-full py-2 px-2 rounded hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-1"></i>Çıkış Yap
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('auth.login') }}" class="block hover:text-[#d4a373] transition-colors">Giriş Yap</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Menu Banner -->
<div class="menu-banner">
    <div class="menu-banner-content">
        <h2 class="text-4xl md:text-5xl font-bold mb-2">Menümüz</h2>
        <p class="text-lg md:text-xl text-gray-200">Lezzetli seçeneklerimizi keşfedin</p>
    </div>
</div>

<!-- Menu Content -->
<div class="max-w-7xl mx-auto py-8 px-4">
    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Dikkat!</strong>
            <span class="block sm:inline">{{ $error }}</span>
        </div>
    @endif

    <!-- Category Sections -->
    @foreach($categories as $category)
        <div class="bg-white rounded-lg shadow-md mb-3 overflow-hidden">
            <!-- Kategori Başlığı -->
            <div class="category-header cursor-pointer flex justify-between items-center" 
                 onclick="toggleCategory('{{ $category->name }}')"
                 data-category="{{ $category->name }}">
                <h3 class="category-title">{{ $category->name }}</h3>
                <i class="fas fa-chevron-down text-[#d4a373] category-icon" id="icon-{{ $category->name }}"></i>
            </div>

            <!-- Kategori İçeriği -->
            <div id="content-{{ $category->name }}" class="menu-content border-t border-gray-100">
                <div class="px-6">
                    <div class="grid menu-grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($category->products as $product)
                            @php $stoktaYok = !$product->stock || !$product->stock->sale_price || !$product->stock->quantity; @endphp
                            <div class="bg-gray-50 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow @if($stoktaYok) opacity-70 border border-red-100 @endif">
                                <div class="product-card">
                                    <h5 class="product-title">{{ $product->name }}</h5>
                                    <p class="product-description text-gray-600">{{ $product->description }}</p>
                                    <div class="flex justify-between items-center">
                                        @if($product->stock && $product->stock->sale_price)
                                            <p class="text-[#d4a373] font-semibold">{{ number_format($product->stock->sale_price, 2) }}₺</p>
                                        @else
                                            <div class="flex items-center gap-2 bg-red-50 border border-red-200 rounded px-2 py-1">
                                                <i class="fas fa-exclamation-circle text-red-400"></i>
                                                <span class="text-red-600 font-semibold">Stokta Yok</span>
                                            </div>
                                        @endif
                                        @if($product->stock && $product->stock->quantity > 0)
                                            <button class="text-[#d4a373] hover:text-[#c48c63] transition-colors" 
                                                    onclick="addToCart({{ $product->id }})">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        @else
                                            <button class="text-gray-300 cursor-not-allowed bg-gray-100 rounded px-2 py-1" disabled title="Stokta yok">
                                                <i class="fas fa-shopping-cart"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @if($product->stock && $product->stock->quantity > 0 && $product->stock->quantity <= 5)
                                        <div class="mt-2">
                                            <span class="text-yellow-600 text-sm">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Son {{ $product->stock->quantity }} adet
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center text-gray-500 py-2">
                                Bu kategoride henüz ürün bulunmamaktadır.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Mobil Mini Sepet -->
<div id="mobile-mini-cart" class="hidden fixed top-[60px] right-0 left-0 bg-white shadow-lg z-50 mx-4 rounded-lg">
    <div class="p-4">
        <h3 class="text-lg font-semibold mb-3">Sepetim</h3>
        <div id="mobile-mini-cart-items" class="max-h-[60vh] overflow-y-auto">
            <!-- Sepet öğeleri buraya dinamik olarak eklenecek -->
        </div>
        <div class="border-t mt-3 pt-3">
            <div class="flex justify-between items-center mb-3">
                <span class="font-semibold">Toplam:</span>
                <span id="mobile-mini-cart-total" class="text-[#d4a373] font-bold">0.00₺</span>
            </div>
            <div class="flex justify-between gap-2">
                <a href="{{ route('cart') }}{{ request()->query('table') ? '?table='.request()->query('table') : '' }}" 
                   class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors text-center flex-1">
                    Sepete Git
                </a>
                <button onclick="clearCart()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-[#f5e6d3] text-gray-800 py-6 mt-auto">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p>&copy; 2025 CentralPerk Cafe. All Rights Reserved.</p>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                mobileMenu.classList.toggle('show');
            });

            // Menü dışına tıklandığında menüyü kapat
            document.addEventListener('click', function(event) {
                if (mobileMenu.classList.contains('show') && 
                    !mobileMenu.contains(event.target) && 
                    !mobileMenuButton.contains(event.target)) {
                    mobileMenu.classList.remove('show');
                }
            });

            // Menü içindeki linklere tıklandığında menüyü kapat
            const menuLinks = mobileMenu.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('show');
                });
            });
        }

        // Mobil sepet butonunu dinle
        const mobileCartButton = document.getElementById('mobile-cart-button');
        const mobileMiniCart = document.getElementById('mobile-mini-cart');
        
        if (mobileCartButton) {
            mobileCartButton.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMiniCart.classList.toggle('hidden');
                if (!mobileMiniCart.classList.contains('hidden')) {
                    updateMiniCart(true);
                }
            });
        }

        // Desktop sepet butonunu dinle
        const cartButton = document.getElementById('cart-button');
        const miniCart = document.getElementById('mini-cart');
        
        if (cartButton) {
            cartButton.addEventListener('click', function(e) {
                e.stopPropagation();
                miniCart.classList.toggle('hidden');
                if (!miniCart.classList.contains('hidden')) {
                    updateMiniCart(false);
                }
            });
        }

        // Sayfa dışına tıklandığında sepetleri kapat
        document.addEventListener('click', function(e) {
            if (miniCart && !miniCart.contains(e.target) && !e.target.closest('#cart-button')) {
                miniCart.classList.add('hidden');
            }
            if (mobileMiniCart && !mobileMiniCart.contains(e.target) && !e.target.closest('#mobile-cart-button')) {
                mobileMiniCart.classList.add('hidden');
            }
        });

        // Sayfa yüklendiğinde sepet sayacını güncelle
        updateCartCount();

        // İlk kategoriyi aç
        const firstCategory = document.querySelector('.category-header');
        if (firstCategory) {
            const categoryName = firstCategory.getAttribute('data-category');
            toggleCategory(categoryName);
        }

        // Kullanıcı menüsü dropdown (masaüstü)
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');
        if (userMenuButton && userDropdown) {
            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userMenuButton.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    });

    function toggleCategory(categoryName) {
        const content = document.getElementById(`content-${categoryName}`);
        const icon = document.getElementById(`icon-${categoryName}`);
        const allContents = document.querySelectorAll('.menu-content');
        const allIcons = document.querySelectorAll('.category-icon');

        // Diğer tüm kategorileri kapat
        allContents.forEach(item => {
            if (item.id !== `content-${categoryName}`) {
                item.classList.remove('active');
            }
        });

        // Diğer tüm ikonları sıfırla
        allIcons.forEach(item => {
            if (item.id !== `icon-${categoryName}`) {
                item.classList.remove('rotate');
            }
        });

        // Seçili kategoriyi aç/kapat
        content.classList.toggle('active');
        icon.classList.toggle('rotate');

        // Kategori açıldıysa oraya scroll yap
        if (content.classList.contains('active')) {
            const header = document.querySelector(`[data-category="${categoryName}"]`);
            header.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function updateMiniCart(isMobile = false) {
        const cartItems = document.getElementById(isMobile ? 'mobile-mini-cart-items' : 'mini-cart-items');
        const cartTotal = document.getElementById(isMobile ? 'mobile-mini-cart-total' : 'mini-cart-total');
        
        if (!cartItems || !cartTotal) return;

        fetch('/cart/count')
            .then(response => response.json())
            .then(data => {
                cartItems.innerHTML = '';
                
                if (data.cart && Object.keys(data.cart).length > 0) {
                    Object.entries(data.cart).forEach(([id, item]) => {
                        const itemPrice = parseFloat(item.price);
                        const itemQuantity = parseInt(item.quantity);
                        const itemTotal = itemPrice * itemQuantity;
                        
                        cartItems.innerHTML += `
                            <div class="mini-cart-item p-2">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <div class="font-semibold">${item.name}</div>
                                        <div class="text-sm text-gray-600">${itemQuantity} x ${itemPrice.toFixed(2)}₺</div>
                                    </div>
                                    <div class="text-[#d4a373] font-bold ml-2">${itemTotal.toFixed(2)}₺</div>
                                </div>
                            </div>
                        `;
                    });

                    cartTotal.textContent = parseFloat(data.total_amount).toFixed(2) + '₺';
                } else {
                    cartItems.innerHTML = '<div class="text-center text-gray-500 py-4">Sepetiniz boş</div>';
                    cartTotal.textContent = '0.00₺';
                }

                // Sepet sayacını güncelle
                document.querySelectorAll('.cart-count').forEach(counter => {
                    counter.textContent = data.cart_count;
                    counter.style.display = data.cart_count > 0 ? 'flex' : 'none';
                });
            })
            .catch(error => {
                console.error('Mini cart update error:', error);
                cartItems.innerHTML = '<div class="text-center text-red-500 py-4">Sepet yüklenirken bir hata oluştu</div>';
            });
    }

    function updateCartCount() {
        fetch('/cart/count')
            .then(response => response.json())
            .then(data => {
                // Tüm sepet sayaçlarını güncelle
                document.querySelectorAll('.cart-count').forEach(counter => {
                    counter.textContent = data.cart_count;
                    counter.style.display = data.cart_count > 0 ? 'flex' : 'none';
                });
            })
            .catch(error => console.error('Cart count error:', error));
    }

    function clearCart() {
        const tableId = new URLSearchParams(window.location.search).get('table');
        
        Swal.fire({
            title: 'Emin misiniz?',
            text: "Sepetinizdeki tüm ürünler silinecek!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d4a373',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Evet, temizle',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/cart/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    updateCartCount();
                    updateMiniCart();
                    
                    // Masa numarasını koruyarak sayfayı yenile
                    if (tableId) {
                        window.location.href = `${window.location.pathname}?table=${tableId}`;
                    } else {
                        window.location.reload();
                    }
                    
                    Swal.fire({
                        title: 'Temizlendi!',
                        text: 'Sepetiniz başarıyla temizlendi.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Hata!',
                        text: 'Sepet temizlenirken bir hata oluştu.',
                        icon: 'error',
                        timer: 1500,
                        showConfirmButton: false,
                        position: 'top-end',
                        toast: true
                    });
                });
            }
        });
    }

    function addToCart(productId) {
        const tableId = new URLSearchParams(window.location.search).get('table');
        if (!tableId) {
            Swal.fire({
                title: 'Uyarı!',
                text: 'Lütfen önce bir masa seçimi yapınız.',
                icon: 'warning',
                timer: 2000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
            return;
        }

        fetch(`/cart/add/${productId}?table=${tableId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                updateMiniCart();
                
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message || 'Ürün sepete eklendi!',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            } else {
                Swal.fire({
                    title: 'Uyarı!',
                    text: data.message || 'Ürün eklenirken bir sorun oluştu.',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Hata!',
                text: 'Ürün eklenirken bir hata oluştu.',
                icon: 'error',
                timer: 1500,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        });
    }
</script>

</body>
</html>
