<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Kullanıcı Yönetimi</title>
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
        <div class="container mx-auto max-w-6xl">
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">Kullanıcı Yönetimi</h3>
                <p class="text-gray-600">Çalışanları ve müşterileri yönetin</p>
                
                <!-- Arama Kutusu -->
                <form method="GET" action="" class="mt-4 flex items-center gap-4">
                    <div class="w-3/4">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad, soyad, e-posta veya telefon ile arayın..." class="form-input pl-12 w-full h-12 text-base" style="padding-left:3rem;">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400"></i>
                        </div>
                    </div>
                    <div class="w-1/4">
                        <select name="role" class="form-select w-full">
                            <option value="">Tüm Roller</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="waiter" {{ request('role') == 'waiter' ? 'selected' : '' }}>Garson</option>
                            <option value="kitchen" {{ request('role') == 'kitchen' ? 'selected' : '' }}>Mutfak</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Kasiyer</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Müşteri</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-[#b88b5a] text-white px-6 py-3 rounded-lg hover:bg-[#c48c63] transition-all flex items-center gap-2">
                        <i class="fas fa-filter"></i> Filtrele
                    </button>
                    <button type="button" onclick="window.location='{{ url()->current() }}'" class="ml-2 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-all flex items-center gap-2">
                        <i class="fas fa-times"></i> Temizle
                    </button>
                </form>
            </div>

            <!-- Çalışanlar Tablosu -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="text-xl font-semibold text-gray-800">Çalışanlar</h4>
                        <p class="text-gray-600 text-sm mt-1">Sistem çalışanlarını yönetin</p>
                    </div>
                    <button onclick="openAddUserModal('employee')" class="bg-[#d4a373] text-white px-6 py-3 rounded-lg hover:bg-[#c48c63] transition-all btn-hover flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        Yeni Çalışan Ekle
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-hover">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tl-lg">
                                    Ad Soyad
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    E-posta
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Telefon
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Rol
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tr-lg">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($employees as $employee)
                            <tr class="transition-all">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-[#f5e6d3] rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-[#d4a373]"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $employee->name }} {{ $employee->lastName }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $employee->phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <select onchange="updateRole({{ $employee->id }}, this.value)" 
                                            class="form-select rounded-lg border-gray-200 text-sm focus:border-[#d4a373] focus:ring focus:ring-[#d4a373] focus:ring-opacity-50">
                                        <option value="admin" {{ $employee->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="waiter" {{ $employee->role === 'waiter' ? 'selected' : '' }}>Garson</option>
                                        <option value="kitchen" {{ $employee->role === 'kitchen' ? 'selected' : '' }}>Mutfak</option>
                                        <option value="cashier" {{ $employee->role === 'cashier' ? 'selected' : '' }}>Kasiyer</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="editUser({{ $employee->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-all p-2 hover:bg-blue-50 rounded-lg">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser({{ $employee->id }})" 
                                            class="text-red-600 hover:text-red-900 transition-all p-2 hover:bg-red-50 rounded-lg ml-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-400">Kayıt bulunamadı.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Çalışanlar Sayfalama -->
                <div class="mt-6">
                    <div class="flex justify-end bg-white/40 backdrop-blur-sm p-4 rounded-lg">
                        {{ $employees->appends(request()->all())->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            </div>

            <!-- Müşteriler Tablosu -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h4 class="text-xl font-semibold text-gray-800">Müşteriler</h4>
                        <p class="text-gray-600 text-sm mt-1">Kayıtlı müşterileri yönetin</p>
                    </div>
                    <button onclick="openAddUserModal('customer')" class="bg-[#4ade80] text-white px-6 py-3 rounded-lg hover:bg-[#22c55e] transition-all btn-hover flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        Yeni Müşteri Ekle
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-hover">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tl-lg">
                                    Ad Soyad
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    E-posta
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Telefon
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider rounded-tr-lg">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($customers as $customer)
                            <tr class="transition-all">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-green-50 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-green-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $customer->name }} {{ $customer->lastName }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $customer->phone }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openPromoteModal({{ $customer->id }})" class="text-green-600 hover:text-green-800 transition-all p-2 hover:bg-green-50 rounded-lg mr-2">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button onclick="editUser({{ $customer->id }})" 
                                            class="text-blue-600 hover:text-blue-900 transition-all p-2 hover:bg-blue-50 rounded-lg">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteUser({{ $customer->id }})" 
                                            class="text-red-600 hover:text-red-900 transition-all p-2 hover:bg-red-50 rounded-lg ml-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-400">Kayıt bulunamadı.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Müşteriler Sayfalama -->
                <div class="mt-6">
                    <div class="flex justify-end bg-white/40 backdrop-blur-sm p-4 rounded-lg">
                        {{ $customers->appends(request()->all())->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kullanıcı Ekleme/Düzenleme Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full modal-transition">
        <div class="relative top-20 mx-auto p-8 border w-[500px] shadow-xl rounded-xl bg-white">
            <div class="absolute top-4 right-4">
                <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div>
                <h3 class="text-2xl font-semibold text-gray-800 mb-6" id="modalTitle">Yeni Kullanıcı Ekle</h3>
                <form id="userForm" class="space-y-6">
                    <input type="hidden" id="userId">
                    <input type="hidden" id="userType">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                <i class="fas fa-user text-[#d4a373] mr-2"></i>Ad<span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" placeholder="Adı giriniz" required
                                   class="form-input">
                            <div class="error-message" id="nameError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="lastName">
                                <i class="fas fa-user text-[#d4a373] mr-2"></i>Soyad<span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="lastName" name="lastName" placeholder="Soyadı giriniz" required
                                   class="form-input">
                            <div class="error-message" id="lastNameError"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">
                            <i class="fas fa-envelope text-[#d4a373] mr-2"></i>E-posta<span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" placeholder="E-posta adresini giriniz" required
                               class="form-input">
                        <div class="error-message" id="emailError"></div>
                    </div>
                    
                    <div id="passwordDiv" class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock text-[#d4a373] mr-2"></i>Şifre<span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" placeholder="Şifre giriniz"
                               class="form-input">
                        <div class="error-message" id="passwordError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="phone">
                            <i class="fas fa-phone text-[#d4a373] mr-2"></i>Telefon<span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="phone" name="phone" placeholder="(5XX) XXX-XXXX" required
                               class="form-input">
                        <div class="error-message" id="phoneError"></div>
                    </div>
                    
                    <div id="roleDiv" class="form-group">
                        <label class="form-label" for="role">
                            <i class="fas fa-user-tag text-[#d4a373] mr-2"></i>Rol<span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="" disabled selected>Rol seçiniz</option>
                            <option value="admin">Admin</option>
                            <option value="waiter">Garson</option>
                            <option value="kitchen">Mutfak</option>
                            <option value="cashier">Kasiyer</option>
                        </select>
                        <div class="error-message" id="roleError"></div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" onclick="closeUserModal()" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all btn-hover flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            İptal
                        </button>
                        <button type="submit" id="submitButton"
                                class="px-6 py-3 bg-[#d4a373] text-white rounded-lg hover:bg-[#c48c63] transition-all btn-hover flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Kaydet</span>
                            <div class="loading-spinner hidden"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Çalışan yap modalı -->
    <div id="promoteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full modal-transition z-50">
        <div class="relative top-32 mx-auto p-8 border w-[400px] shadow-xl rounded-xl bg-white">
            <div class="absolute top-4 right-4">
                <button onclick="closePromoteModal()" class="text-gray-400 hover:text-gray-600 transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Çalışan Rolü Ata</h3>
                <form id="promoteForm" class="space-y-6">
                    <input type="hidden" id="promoteUserId">
                    <div class="form-group">
                        <label class="form-label" for="promoteRole">
                            Rol Seçiniz
                        </label>
                        <select id="promoteRole" name="role" class="form-select" required>
                            <option value="" disabled selected>Rol seçiniz</option>
                            <option value="admin">Admin</option>
                            <option value="waiter">Garson</option>
                            <option value="kitchen">Mutfak</option>
                            <option value="cashier">Kasiyer</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" onclick="closePromoteModal()" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all btn-hover flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            İptal
                        </button>
                        <button type="submit" id="promoteSubmitButton"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all btn-hover flex items-center gap-2">
                            <i class="fas fa-check"></i>
                            <span>Onayla</span>
                            <div class="loading-spinner hidden"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Mesajları -->
    <div id="toast" class="toast" role="alert"></div>

    <style>
        .transition-all {
            transition: all 0.3s ease;
        }
        .table-hover tr:hover {
            background-color: #f8fafc;
        }
        .btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .modal-transition {
            transition: opacity 0.3s ease-out;
        }
        .form-input, .form-select {
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            width: 100%;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .form-input[type="text"]#searchInput {
            padding-left: 3rem;
        }
        .form-input:hover, .form-select:hover {
            background-color: #fff;
            border-color: #d4a373;
        }
        .form-input:focus, .form-select:focus {
            background-color: #fff;
            border-color: #d4a373;
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.2);
            outline: none;
        }
        .form-label {
            color: #4b5563;
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-group {
            position: relative;
        }
        .form-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        .form-input-with-icon {
            padding-left: 2.75rem;
        }
        
        /* Toast Mesajları için Stiller */
        .toast {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            color: white;
            transform: translateY(100%);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .toast-success {
            background-color: #4ade80;
        }
        
        .toast-error {
            background-color: #ef4444;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }
        
        .loading-spinner.hidden {
            display: none;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Form Validation */
        .form-input.error {
            border-color: #ef4444;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        /* Disabled Button State */
        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>

    <script src="https://unpkg.com/imask"></script>
    <script>
        let currentUserId = null;
        let currentUserType = null;

        function openAddUserModal(type) {
            currentUserId = null;
            currentUserType = type;
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = type === 'employee' ? 'Yeni Çalışan Ekle' : 'Yeni Müşteri Ekle';
            document.getElementById('userForm').reset();
            document.getElementById('userType').value = type;
            
            // Rol ve şifre alanlarını ayarla
            const roleDiv = document.getElementById('roleDiv');
            const passwordDiv = document.getElementById('passwordDiv');
            const roleSelect = document.getElementById('role');
            const passwordInput = document.getElementById('password');
            
            if (type === 'employee') {
                roleDiv.style.display = 'block';
                roleSelect.required = true;
                passwordDiv.style.display = 'block';
                passwordInput.required = true;
            } else {
                roleDiv.style.display = 'none';
                roleSelect.required = false;
                passwordDiv.style.display = 'block';
                passwordInput.required = true;
            }
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function editUser(id) {
            currentUserId = id;
            
            fetch(`/admin/users/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 404) {
                        throw new Error('Kullanıcı bulunamadı');
                    }
                    throw new Error('Sunucu hatası oluştu');
                }
                return response.json();
            })
            .then(data => {
                if (!data || typeof data !== 'object') {
                    throw new Error('Geçersiz veri formatı');
                }

                document.getElementById('userId').value = data.id;
                document.getElementById('name').value = data.name || '';
                document.getElementById('lastName').value = data.lastName || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('phone').value = data.phone || '';
                
                // Kullanıcı tipini belirle ve sakla
                currentUserType = data.role === 'customer' ? 'customer' : 'employee';
                document.getElementById('userType').value = currentUserType;
                
                // Rol ve şifre alanlarını ayarla
                const roleDiv = document.getElementById('roleDiv');
                const passwordDiv = document.getElementById('passwordDiv');
                const roleSelect = document.getElementById('role');
                const passwordInput = document.getElementById('password');
                
                if (currentUserType === 'employee') {
                    roleDiv.style.display = 'block';
                    roleSelect.required = true;
                    roleSelect.value = data.role || '';
                    passwordDiv.style.display = 'none';
                    passwordInput.required = false;
                    passwordInput.value = '';
                } else {
                    roleDiv.style.display = 'none';
                    roleSelect.required = false;
                    roleSelect.value = '';
                    passwordDiv.style.display = 'none';
                    passwordInput.required = false;
                    passwordInput.value = '';
                }
                
                document.getElementById('modalTitle').textContent = 'Kullanıcı Düzenle';
                document.getElementById('userModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Hata:', error);
                showToast(error.message || 'Kullanıcı bilgileri alınırken bir hata oluştu', 'error');
            });
        }

        function deleteUser(userId) {
            Swal.fire({
                title: 'Emin misiniz?',
                text: "Bu kullanıcıyı silmek istediğinizden emin misiniz?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d4a373',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Evet, sil',
                cancelButtonText: 'İptal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/users/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Başarılı!',
                                text: 'Kullanıcı başarıyla silindi.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Sayfayı yenile
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Hata!',
                                text: data.message || 'Kullanıcı silinirken bir hata oluştu.',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Hata!',
                            text: 'Kullanıcı silinirken bir hata oluştu.',
                            icon: 'error'
                        });
                    });
                }
            });
        }

        function updateRole(id, role) {
            fetch(`/admin/users/${id}/role`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    role: role,
                    _method: 'PUT'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Rol güncellenirken bir hata oluştu');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast('Rol başarıyla güncellendi', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Rol güncellenirken bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                showToast(error.message || 'Rol güncellenirken bir hata oluştu', 'error');
            });
        }

        // Toast mesajı gösterme fonksiyonu
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast toast-${type}`;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Telefon numarası maskesi
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            const maskOptions = {
                mask: '(500) 000-0000',
                lazy: false
            };
            const mask = IMask(phoneInput, maskOptions);
        });

        // Form validasyonu güncelleme
        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitButton = document.getElementById('submitButton');
            const submitText = submitButton.querySelector('span');
            const loadingSpinner = submitButton.querySelector('.loading-spinner');
            
            // Hata mesajlarını temizle
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.form-input, .form-select').forEach(el => el.classList.remove('error'));
            
            try {
                // Form gönderilmeden önce loading durumunu ayarla
                submitButton.disabled = true;
                submitText.textContent = 'Kaydediliyor...';
                loadingSpinner.classList.remove('hidden');

                // Form verilerini topla
                const formData = {
                    name: document.getElementById('name').value.trim(),
                    lastName: document.getElementById('lastName').value.trim(),
                    email: document.getElementById('email').value.trim(),
                    phone: document.getElementById('phone').value.replace(/\D/g, ''),
                    role: currentUserType === 'customer' ? 'customer' : document.getElementById('role').value
                };

                // Telefon numarası kontrolü
                if (formData.phone.length !== 10) {
                    throw new Error('Telefon numarası 10 haneli olmalıdır');
                }

                // Yeni kullanıcı ekleme durumunda şifre kontrolü
                if (!currentUserId) {
                    const password = document.getElementById('password').value;
                    if (!password) {
                        throw new Error('Şifre alanı zorunludur');
                    }
                    formData.password = password;
                }

                // URL ve method belirleme
                const url = currentUserId ? `/admin/users/${currentUserId}` : '/admin/users';
                const method = currentUserId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                
                if (data.success) {
                    showToast('İşlem başarıyla tamamlandı', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Bir hata oluştu');
                }
            } catch (error) {
                console.error('Hata:', error);
                showToast(error.message || 'Bir hata oluştu', 'error');
            } finally {
                // Her durumda loading durumunu sıfırla
                submitButton.disabled = false;
                submitText.textContent = 'Kaydet';
                loadingSpinner.classList.add('hidden');
            }
        });

        function openPromoteModal(userId) {
            document.getElementById('promoteUserId').value = userId;
            document.getElementById('promoteRole').value = '';
            document.getElementById('promoteModal').classList.remove('hidden');
        }

        function closePromoteModal() {
            document.getElementById('promoteModal').classList.add('hidden');
        }

        document.getElementById('promoteForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const userId = document.getElementById('promoteUserId').value;
            const role = document.getElementById('promoteRole').value;
            const submitButton = document.getElementById('promoteSubmitButton');
            const submitText = submitButton.querySelector('span');
            const loadingSpinner = submitButton.querySelector('.loading-spinner');
            if (!role) return;
            try {
                submitButton.disabled = true;
                submitText.textContent = 'Kaydediliyor...';
                loadingSpinner.classList.remove('hidden');
                const response = await fetch(`/admin/users/${userId}/role`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        role: role,
                        _method: 'PUT'
                    })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Rol başarıyla atandı', 'success');
                    setTimeout(() => { location.reload(); }, 1000);
                } else {
                    throw new Error(data.message || 'Rol atanırken bir hata oluştu');
                }
            } catch (error) {
                showToast(error.message || 'Rol atanırken bir hata oluştu', 'error');
            } finally {
                submitButton.disabled = false;
                submitText.textContent = 'Onayla';
                loadingSpinner.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 