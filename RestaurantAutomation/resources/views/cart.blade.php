<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Sepetim</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @media (max-width: 768px) {
            .cart-table {
                display: block;
                padding: 0.5rem;
            }
            .cart-table thead {
                display: none;
            }
            .cart-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                padding: 1.25rem;
                background-color: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }
            .cart-table tbody td {
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                padding: 0.5rem 0;
                border: none;
                font-size: 1rem;
            }
            .cart-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #4b5563;
            }
            .cart-table tbody td:not(:last-child) {
                border-bottom: 1px solid #f3f4f6;
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
            }
            .cart-actions {
                display: flex;
                justify-content: flex-end;
                padding-top: 0.75rem;
                margin-top: 0.75rem;
                border-top: 1px solid #f3f4f6;
            }
            .cart-product-name {
                font-weight: 600;
                color: #1f2937;
                font-size: 1.1rem;
                margin-bottom: 0.25rem;
            }
            .cart-quantity {
                color: #6b7280;
                font-size: 1rem;
                text-align: right;
            }
            .cart-price {
                color: #374151;
                font-size: 1rem;
                text-align: right;
            }
            .cart-total {
                font-weight: 600;
                color: #1f2937;
                font-size: 1.1rem;
                text-align: right;
            }
            .cart-summary {
                background-color: white;
                border-radius: 0.75rem;
                padding: 1.5rem;
                margin: 1rem 0.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }
            .cart-summary .flex {
                flex-direction: column;
                gap: 1rem;
            }
            .cart-summary .text-xl {
                font-size: 1.25rem;
                text-align: center;
                margin-bottom: 0.5rem;
            }
            button[type="submit"].bg-[#d4a373] {
                width: 100%;
                padding: 1rem;
                font-size: 1.1rem;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .cart-table tbody tr {
                padding: 1rem;
            }
            .cart-table tbody td {
                font-size: 0.95rem;
            }
            .cart-product-name {
                font-size: 1rem;
            }
            .cart-quantity,
            .cart-price,
            .cart-total {
                font-size: 0.95rem;
            }
            .cart-summary {
                padding: 1.25rem;
            }
            button[type="submit"].bg-[#d4a373] {
                padding: 0.875rem;
                font-size: 1rem;
            }
        }

        .cart-item {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 100%;
            max-width: 300px;
            margin: 1rem auto;
        }

        .cart-item-info {
            display: grid;
            grid-template-columns: auto auto;
            gap: 1rem;
            width: 100%;
            margin-bottom: 1rem;
        }

        .cart-item-label {
            text-align: left;
            color: #666;
            font-weight: 500;
        }

        .cart-item-value {
            text-align: right;
            font-weight: 600;
            color: #333;
        }

        .delete-button {
            color: #ef4444;
            transition: color 0.3s;
            margin-top: 0.5rem;
        }

        .delete-button:hover {
            color: #dc2626;
        }

        /* Toast bildirimleri için stil */
        .swal2-toast {
            background: #4CAF50 !important;
            color: white !important;
            padding: 1rem !important;
        }

        .swal2-toast .swal2-title {
            color: white !important;
            font-size: 1.1rem !important;
        }

        .swal2-toast .swal2-html-container {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 0.95rem !important;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-[#f5e6d3] p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="text-2xl font-bold flex items-center gap-1">
            <a href="{{ route('menu') }}" class="flex items-center gap-1">
                Central<sup><i class="fa-solid fa-mug-saucer text-[#d4a373]"></i></sup>Perk <span class="text-gray-600 text-lg">cafe</span>
            </a>
        </div>
        <a href="javascript:void(0)" onclick="returnToMenu()" class="text-[#d4a373] hover:text-[#c48c63] transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Menüye Dön
        </a>
    </div>
</nav>

<!-- Cart Content -->
<div class="flex-grow container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-center mb-8">Sepetiniz</h1>
    
    @if(session('cart') && count(session('cart')) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
            @foreach(session('cart') as $id => $item)
                <div class="cart-item" data-id="{{ $id }}">
                    <div class="cart-item-info">
                        <span class="cart-item-label">Ürün</span>
                        <span class="cart-item-value">{{ $item['name'] }}</span>
                        
                        <span class="cart-item-label">Adet</span>
                        <div class="cart-item-value flex items-center justify-end space-x-2">
                            <button onclick="updateQuantity({{ $id }}, 'decrease')" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                                <i class="fas fa-minus text-sm text-gray-600"></i>
                            </button>
                            <span class="quantity-value">{{ $item['quantity'] }}</span>
                            <button onclick="updateQuantity({{ $id }}, 'increase')" class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                                <i class="fas fa-plus text-sm text-gray-600"></i>
                            </button>
                        </div>
                        
                        <span class="cart-item-label">Fiyat</span>
                        <span class="cart-item-value">{{ number_format($item['price'], 2) }}₺</span>
                        
                        <span class="cart-item-label">Toplam</span>
                        <span class="cart-item-value item-total">{{ number_format($item['price'] * $item['quantity'], 2) }}₺</span>
                    </div>
                    
                    <button onclick="removeFromCart({{ $id }})" class="delete-button">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-8">
            @if(request()->query('table'))
            <div class="mb-3 border-b border-gray-200 pb-3">
                <span class="text-gray-700 text-base"><i class="fas fa-chair text-[#d4a373] mr-2"></i> Masa {{ request()->query('table') }}</span>
                <input type="hidden" id="table_id" value="{{ request()->query('table') }}">
            </div>
            @endif
            
            <div class="inline-block bg-white rounded-lg shadow-md p-6">
                <div class="text-xl font-bold mb-4 cart-total-amount">
                    Toplam Tutar: {{ number_format(array_sum(array_map(function($item) {
                        return $item['price'] * $item['quantity'];
                    }, session('cart'))), 2) }}₺
                </div>
                
                <div class="flex flex-col gap-3">
                    <!-- Sepeti Temizle butonu -->
                    <button type="button" onclick="clearCart()" class="bg-[#c48c63] text-white px-6 py-2 rounded hover:bg-[#b37952] transition-colors">
                        <i class="fas fa-trash mr-2"></i>Sepeti Temizle
                    </button>
                    
                    <!-- Siparişi Oluştur butonu -->
                    @csrf
                    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
                    @if(request()->query('table'))
                    <input type="hidden" id="table_id" value="{{ request()->query('table') }}">
                    @endif
                    <button type="button" onclick="createOrder()" class="bg-[#d4a373] text-white px-6 py-2 rounded hover:bg-[#c48c63] transition-colors">
                        <i class="fas fa-utensils mr-2"></i>Siparişi Oluştur
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
            <p class="text-xl text-gray-600">Sepetiniz boş</p>
            <a href="{{ route('menu') }}{{ request()->query('table') ? '?table='.request()->query('table') : '' }}" class="inline-block mt-4 text-[#d4a373] hover:text-[#c48c63] transition-colors">
                Menüye dön ve alışverişe başla
            </a>
        </div>
    @endif
</div>

<!-- Footer -->
<footer class="bg-[#f5e6d3] text-gray-800 py-6">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <p>&copy; 2025 CentralPerk Cafe. All Rights Reserved.</p>
    </div>
</footer>

<script>
    function removeFromCart(id) {
        fetch(`/cart/remove/${id}`, {
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
                // Ürünü DOM'dan kaldır
                const cartItem = document.querySelector(`[data-id="${id}"]`);
                if (cartItem) {
                    cartItem.remove();
                }

                // Sepet sayacını güncelle
                const cartCountElements = document.querySelectorAll(".cart-count");
                cartCountElements.forEach(element => {
                    element.textContent = data.cart_count;
                    element.style.display = data.cart_count > 0 ? 'inline-flex' : 'none';
                });

                // Eğer sepet boşsa sayfayı yenile
                if (data.cart_count === 0) {
                    location.reload();
                } else {
                    // Toplam tutarı güncelle
                    updateCartTotal();
                }

                // Başarı mesajı göster
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message,
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            Swal.fire({
                title: 'Hata!',
                text: 'Ürün kaldırılırken bir hata oluştu.',
                icon: 'error',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        });
    }

    function updateCartTotal() {
        const cartItems = document.querySelectorAll('.cart-item');
        let total = 0;

        cartItems.forEach(item => {
            const priceText = item.querySelector('.item-total').textContent;
            const price = parseFloat(priceText.replace('₺', '').replace(',', '.'));
            total += price;
        });

        const cartTotalElement = document.querySelector('.cart-total-amount');
        if (cartTotalElement) {
            cartTotalElement.textContent = `Toplam Tutar: ${total.toFixed(2)}₺`;
        }
    }

    function createOrder() {
        // SweetAlert ile işlem bilgisi göster
        Swal.fire({
            title: 'İşleniyor...',
            text: 'Siparişiniz oluşturuluyor, lütfen bekleyin.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Masa bilgisini al
        const tableId = document.getElementById('table_id') ? document.getElementById('table_id').value : '';
        const csrfToken = document.getElementById('csrf_token').value;
        
        // API isteği gönder
        fetch('/cart/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: '_token=' + encodeURIComponent(csrfToken) + 
                  (tableId ? '&table_id=' + encodeURIComponent(tableId) : '')
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message || 'Siparişiniz başarıyla oluşturuldu!',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        const redirectUrl = tableId ? '/menu?table=' + tableId : '/menu';
                        window.location.href = redirectUrl;
                    }
                });
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: data.message || 'Sipariş oluşturulurken bir hata oluştu.',
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            Swal.fire({
                title: 'Hata!',
                text: 'Sipariş işlenirken bir hata oluştu. Lütfen tekrar deneyin.',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
        });
    }

    function updateQuantity(id, action) {
        const formData = new URLSearchParams();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('action', action);

        fetch(`/cart/update/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData.toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Sayfayı yenilemek yerine sadece ilgili değerleri güncelle
                const cartItem = document.querySelector(`[data-id="${id}"]`);
                if (cartItem) {
                    const quantityElement = cartItem.querySelector('.quantity-value');
                    const totalElement = cartItem.querySelector('.item-total');
                    if (quantityElement && totalElement) {
                        quantityElement.textContent = data.quantity;
                        totalElement.textContent = `${data.total.toFixed(2)}₺`;
                    }
                }

                // Genel toplamı güncelle
                const cartTotalElement = document.querySelector('.cart-total-amount');
                if (cartTotalElement) {
                    cartTotalElement.textContent = `Toplam Tutar: ${data.cart_total.toFixed(2)}₺`;
                }

                // Eğer miktar 0'a düştüyse ürünü kaldır
                if (data.quantity === 0) {
                    cartItem?.remove();
                    // Sepet boşsa mesajı göster
                    if (data.cart_total === 0) {
                        location.reload();
                    }
                }

                // Sepet sayacını güncelle
                const cartCountElements = document.querySelectorAll(".cart-count");
                cartCountElements.forEach(element => {
                    element.textContent = data.cart_count;
                    element.style.display = data.cart_count > 0 ? 'inline-flex' : 'none';
                });

                Swal.fire({
                    title: 'Başarılı!',
                    text: 'Sepet güncellendi!',
                    icon: 'success',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                });
            }
        })
        .catch(error => {
            console.error('Hata:', error);
            Swal.fire({
                title: 'Hata!',
                text: 'Sepet güncellenirken bir hata oluştu.',
                icon: 'error',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        });
    }

    function returnToMenu() {
        window.history.back();
    }

    function clearCart() {
        Swal.fire({
            title: 'Emin misiniz?',
            text: 'Sepetinizdeki tüm ürünler silinecek!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Evet, temizle',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/cart/clear', {
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
                        Swal.fire({
                            title: 'Başarılı!',
                            text: 'Sepetiniz temizlendi!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Hata!',
                            text: data.message || 'Sepet temizlenirken bir hata oluştu.',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    Swal.fire({
                        title: 'Hata!',
                        text: 'Sepet temizlenirken bir hata oluştu.',
                        icon: 'error'
                    });
                });
            }
        });
    }
</script>
</body>
</html>