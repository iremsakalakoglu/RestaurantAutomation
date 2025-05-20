<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Kategori Yönetimi</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* TailwindCSS için özel stiller */
        @tailwind base;
        @tailwind components;
        @tailwind utilities;
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
            <h1 class="text-2xl font-bold">Kategori Yönetimi</h1>
            <button onclick="openAddCategoryModal()" class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                <i class="fas fa-plus mr-2"></i>Yeni Kategori Ekle
            </button>
        </div>

        <!-- Kategori Listesi -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($categories as $category)
            <div class="bg-white rounded-lg shadow-md overflow-hidden category-item" data-name="{{ $category->name }}">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            @if($category->icon_type === 'custom')
                                <img src="{{ asset('storage/' . $category->icon) }}" 
                                     alt="{{ $category->name }}" 
                                     class="w-12 h-12 rounded-full object-cover"
                                     onerror="this.src='{{ asset('images/default-category.png') }}'; this.onerror=null;">
                            @else
                                <i class="fas {{ $category->icon }} text-[#d4a373] text-xl"></i>
                            @endif
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $category->products_count }} Ürün</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editCategory({{ $category->id }})" class="text-[#d4a373] hover:text-[#c48c63]">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCategory({{ $category->id }})" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Son Güncelleme:</span>
                            <span>{{ $category->updated_at ? $category->updated_at->format('d.m.Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-8 text-gray-500">
                Henüz kategori bulunmamaktadır.
            </div>
            @endforelse
        </div>
        
        <!-- Sayfalama -->
        <div class="fixed bottom-8 left-1/2 transform -translate-x-1/2 bg-white p-6 rounded-lg shadow-md w-auto" style="font-size: 1.1rem;">
            <div class="flex justify-center">
                {{ $categories->links() }}
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
        // Sayfa yüklendiğinde iconları göster
        document.addEventListener('DOMContentLoaded', function() {
            const categories = document.querySelectorAll('.category-item');
            categories.forEach(category => {
                const categoryName = category.dataset.name;
                const iconElement = category.querySelector('.category-icon');
                if (iconElement) { // Eğer Font Awesome ikonu varsa
                    const savedIcon = localStorage.getItem('category_icon_' + categoryName) || getDefaultIcon(categoryName);
                    iconElement.className = `fas ${savedIcon} text-[#d4a373] text-xl`;
                }
            });
        });

        function getDefaultIcon(categoryName) {
            const defaultIcons = {
                'Kahve': 'fa-mug-hot',
                'İçecek': 'fa-glass-martini-alt',
                'Tatlı': 'fa-cookie-bite',
                'Yemek': 'fa-utensils',
                'Kahvaltı': 'fa-bread-slice',
                'Dondurma': 'fa-ice-cream',
                'Atıştırmalık': 'fa-pizza-slice',
                'Sıcak İçecek': 'fa-mug-hot'
            };
            return defaultIcons[categoryName] || localStorage.getItem('category_icon_' + categoryName) || 'fa-utensils';
        }

        function toggleIconOptions() {
            const iconType = document.getElementById('iconType').value;
            const defaultSection = document.getElementById('defaultIconSection');
            const customSection = document.getElementById('customIconSection');
            
            if (iconType === 'default') {
                defaultSection.classList.remove('hidden');
                customSection.classList.add('hidden');
            } else {
                defaultSection.classList.add('hidden');
                customSection.classList.remove('hidden');
            }
        }

        // Görsel önizleme için event listener
        document.addEventListener('DOMContentLoaded', function() {
            const customIcon = document.getElementById('customIcon');
            if (customIcon) {
                customIcon.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) { // 2MB kontrol
                            Swal.fire({
                                title: 'Hata!',
                                text: 'Dosya boyutu 2MB\'dan küçük olmalıdır.',
                                icon: 'error'
                            });
                            e.target.value = '';
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const preview = document.getElementById('iconPreview');
                            const previewImage = document.getElementById('previewImage');
                            preview.classList.remove('hidden');
                            previewImage.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        });

        function openAddCategoryModal() {
            Swal.fire({
                title: 'Yeni Kategori Ekle',
                html: `
                    <form id="addCategoryForm" class="text-left">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
                            <input type="text" id="categoryName" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Kategori adını girin">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">İkon Tipi</label>
                            <select id="iconType" onchange="toggleIconOptions()" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                                <option value="default">Hazır İkon</option>
                                <option value="custom">Özel Görsel</option>
                            </select>
                            <div id="defaultIconSection">
                                <select id="categoryIcon" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="fa-mug-hot">☕ Kahve</option>
                                    <option value="fa-glass-martini-alt">🥤 İçecek</option>
                                    <option value="fa-cookie-bite">🍪 Tatlı</option>
                                    <option value="fa-utensils">🍽️ Yemek</option>
                                    <option value="fa-bread-slice">🥐 Kahvaltı</option>
                                    <option value="fa-ice-cream">🍦 Dondurma</option>
                                    <option value="fa-pizza-slice">🍕 Atıştırmalık</option>
                                </select>
                            </div>
                            <div id="customIconSection" class="hidden">
                                <input type="file" id="customIcon" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG veya GIF (max. 2MB)</p>
                                <div id="iconPreview" class="mt-2 hidden">
                                    <img id="previewImage" class="h-12 w-12 object-cover rounded-full">
                                </div>
                            </div>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Kaydet',
                cancelButtonText: 'İptal',
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#6b7280',
                preConfirm: () => {
                    const name = document.getElementById('categoryName').value.trim();
                    const iconType = document.getElementById('iconType').value;
                    const formData = new FormData();
                    formData.append('name', name);
                    formData.append('icon_type', iconType);
                    
                    if (!name) {
                        Swal.showValidationMessage('Kategori adı zorunludur');
                        return false;
                    }

                    if (iconType === 'default') {
                        formData.append('icon', document.getElementById('categoryIcon').value);
                    } else {
                        const customIcon = document.getElementById('customIcon').files[0];
                        if (!customIcon) {
                            Swal.showValidationMessage('Lütfen bir görsel seçin');
                            return false;
                        }
                        formData.append('icon', customIcon);
                    }

                    return fetch('{{ route("admin.categories.store") }}', {
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
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        }

        function editCategory(id) {
            fetch(`/admin/categories/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Kategori bilgileri alınamadı');
                    }
                    
                    const category = data.category;
                    
                    Swal.fire({
                        title: 'Kategori Düzenle',
                        html: `
                            <form id="editCategoryForm" class="text-left">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı</label>
                                    <input type="text" id="editCategoryName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="${category.name}">
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">İkon Tipi</label>
                                    <select id="editIconType" onchange="toggleEditIconOptions()" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                                        <option value="default" ${category.icon_type === 'default' ? 'selected' : ''}>Hazır İkon</option>
                                        <option value="custom" ${category.icon_type === 'custom' ? 'selected' : ''}>Özel Görsel</option>
                                    </select>
                                    <div id="editDefaultIconSection" class="${category.icon_type === 'custom' ? 'hidden' : ''}">
                                        <select id="editCategoryIcon" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                            <option value="fa-mug-hot" ${category.icon === 'fa-mug-hot' ? 'selected' : ''}>☕ Kahve</option>
                                            <option value="fa-glass-martini-alt" ${category.icon === 'fa-glass-martini-alt' ? 'selected' : ''}>🥤 İçecek</option>
                                            <option value="fa-cookie-bite" ${category.icon === 'fa-cookie-bite' ? 'selected' : ''}>🍪 Tatlı</option>
                                            <option value="fa-utensils" ${category.icon === 'fa-utensils' ? 'selected' : ''}>🍽️ Yemek</option>
                                            <option value="fa-bread-slice" ${category.icon === 'fa-bread-slice' ? 'selected' : ''}>🥐 Kahvaltı</option>
                                            <option value="fa-ice-cream" ${category.icon === 'fa-ice-cream' ? 'selected' : ''}>🍦 Dondurma</option>
                                            <option value="fa-pizza-slice" ${category.icon === 'fa-pizza-slice' ? 'selected' : ''}>🍕 Atıştırmalık</option>
                                        </select>
                                    </div>
                                    <div id="editCustomIconSection" class="${category.icon_type === 'default' ? 'hidden' : ''}">
                                        <input type="file" id="editCustomIcon" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG veya GIF (max. 2MB)</p>
                                        ${category.icon_type === 'custom' && category.icon ? `
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">Mevcut Görsel:</p>
                                                <img src="/storage/${category.icon}" class="h-12 w-12 object-cover rounded-full mt-1">
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Güncelle',
                        cancelButtonText: 'İptal',
                        confirmButtonColor: '#d4a373',
                        cancelButtonColor: '#6b7280',
                        preConfirm: () => {
                            const name = document.getElementById('editCategoryName').value.trim();
                            const iconType = document.getElementById('editIconType').value;
                            const formData = new FormData();
                            
                            formData.append('name', name);
                            formData.append('icon_type', iconType);
                            formData.append('_method', 'PUT');
                            
                            if (!name) {
                                Swal.showValidationMessage('Kategori adı zorunludur');
                                return false;
                            }

                            if (iconType === 'default') {
                                formData.append('icon', document.getElementById('editCategoryIcon').value);
                            } else {
                                const customIcon = document.getElementById('editCustomIcon').files[0];
                                if (customIcon) {
                                    formData.append('icon', customIcon);
                                }
                            }

                            return fetch(`/admin/categories/${id}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message || 'Kategori güncellenirken bir hata oluştu');
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
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Hata!',
                        text: error.message || 'Kategori bilgileri alınamadı',
                        icon: 'error'
                    });
                });
        }

        function toggleEditIconOptions() {
            const iconType = document.getElementById('editIconType').value;
            const defaultSection = document.getElementById('editDefaultIconSection');
            const customSection = document.getElementById('editCustomIconSection');
            
            if (iconType === 'default') {
                defaultSection.classList.remove('hidden');
                customSection.classList.add('hidden');
            } else {
                defaultSection.classList.add('hidden');
                customSection.classList.remove('hidden');
            }
        }

        function deleteCategory(id) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Bu kategoriyi silmek istediğinizden emin misiniz?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Evet, Sil',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/categories/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Hata!',
                            text: error.message || 'Kategori silinirken bir hata oluştu',
                            icon: 'error'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>