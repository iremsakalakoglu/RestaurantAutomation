<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->name ?? 'Restaurant' }} - Ayarlar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Ayarlar</h1>
        </div>

        <!-- Ayarlar Kartları -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Genel Ayarlar -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Genel Ayarlar</h2>
                    <form id="generalSettingsForm">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">İşletme Adı</label>
                                <input type="text" id="businessName" value="{{ $settings->name ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                                <textarea id="businessAddress" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2">{{ $settings->address ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="tel" id="businessPhone" value="{{ $settings->phone ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                <input type="email" id="businessEmail" value="{{ $settings->email ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <button type="button" onclick="saveGeneralSettings()" class="w-full bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tema Ayarları (sola) -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden order-1 md:order-1">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Aylık Gelir-Gider Raporu</h2>
                    <form id="incomeExpenseForm" class="flex flex-col h-full">
                        <div class="space-y-4 flex-1">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ay</label>
                                <select id="reportMonth" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option>Ocak</option>
                                    <option>Şubat</option>
                                    <option>Mart</option>
                                    <option>Nisan</option>
                                    <option>Mayıs</option>
                                    <option>Haziran</option>
                                    <option>Temmuz</option>
                                    <option>Ağustos</option>
                                    <option>Eylül</option>
                                    <option>Ekim</option>
                                    <option>Kasım</option>
                                    <option>Aralık</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yıl</label>
                                <input type="number" id="reportYear" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ date('Y') }}">
                            </div>
                        </div>
                        <button type="button" onclick="getIncomeExpenseReport()" class="w-full bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors mt-8">
                            Raporu Göster
                        </button>
                    </form>
                </div>
            </div>

            <!-- Şifre Yenileme (sağa) -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden order-2 md:order-2">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Şifre Yenileme</h2>
                    <form id="passwordChangeForm">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mevcut Şifre</label>
                                <input type="password" id="currentPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre</label>
                                <input type="password" id="newPassword" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yeni Şifre (Tekrar)</label>
                                <input type="password" id="newPasswordRepeat" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <button type="button" onclick="changePassword()" class="w-full bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kullanıcı Bilgileri -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Kullanıcı Bilgileri</h2>
                    <form id="userInfoForm">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ad</label>
                                <input type="text" id="userFirstName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ Auth::user()->name }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Soyad</label>
                                <input type="text" id="userLastName" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ Auth::user()->lastName ?? '' }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                                <input type="email" id="userEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ Auth::user()->email }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="text" id="userPhone" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ Auth::user()->phone ?? '' }}">
                            </div>
                            <button type="button" onclick="saveUserInfo()" class="w-full bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-colors">
                                Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saveGeneralSettings() {
            const name = document.getElementById('businessName').value;
            const address = document.getElementById('businessAddress').value;
            const phone = document.getElementById('businessPhone').value;
            const email = document.getElementById('businessEmail').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/admin/settings/general', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    address,
                    phone,
                    email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Genel ayarlar kaydedildi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Tüm sayfayı yenile
                        window.location.href = window.location.href;
                    });
                } else {
                    Swal.fire({
                        title: 'Hata!',
                        text: data.message || 'Bir hata oluştu',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Hata!',
                    text: error.message || 'Bir hata oluştu',
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }

        function saveUserInfo() {
            const firstName = document.getElementById('userFirstName').value;
            const lastName = document.getElementById('userLastName').value;
            const email = document.getElementById('userEmail').value;
            const phone = document.getElementById('userPhone').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/account-info/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: firstName,
                    last_name: lastName,
                    email: email,
                    phone: phone
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Kullanıcı bilgileri güncellendi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Hata!',
                        text: data.message || 'Bir hata oluştu',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Hata!',
                    text: error.message || 'Bir hata oluştu',
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }

        function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const newPasswordRepeat = document.getElementById('newPasswordRepeat').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (newPassword !== newPasswordRepeat) {
                Swal.fire({
                    title: 'Hata!',
                    text: 'Yeni şifreler eşleşmiyor',
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            fetch('/account-info/password-update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    new_password_confirmation: newPasswordRepeat
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Şifre başarıyla değiştirildi',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Hata!',
                        text: data.message || 'Bir hata oluştu',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Hata!',
                    text: error.message || 'Bir hata oluştu',
                    icon: 'error',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }

        function getIncomeExpenseReport() {
            const month = document.getElementById('reportMonth').value;
            const year = document.getElementById('reportYear').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin/reports/income-expense', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ month, year })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let detailsTable = '';
                    if (data.details && data.details.length > 0) {
                        detailsTable = `
                            <div style="max-height:350px;overflow-y:auto;">
                                <table class='min-w-full text-xs mt-4'><thead><tr><th class='px-2 py-1'>Tarih</th><th class='px-2 py-1'>Tip</th><th class='px-2 py-1'>Miktar</th><th class='px-2 py-1'>Alış Fiyatı</th><th class='px-2 py-1'>Satış Fiyatı</th><th class='px-2 py-1'>Açıklama</th></tr></thead><tbody>`;
                        data.details.forEach(item => {
                            detailsTable += `<tr><td class='border px-2 py-1'>${item.created_at ? item.created_at.substring(0,10) : ''}</td><td class='border px-2 py-1'>${item.type}</td><td class='border px-2 py-1'>${item.quantity}</td><td class='border px-2 py-1'>${item.purchase_price ?? '-'}</td><td class='border px-2 py-1'>${item.sale_price ?? '-'}</td><td class='border px-2 py-1'>${item.description ?? ''}</td></tr>`;
                        });
                        detailsTable += '</tbody></table></div>';
                    } else {
                        detailsTable = '<div class="text-gray-500 mt-2">Bu ay için hareket yok.</div>';
                    }
                    Swal.fire({
                        title: 'Aylık Gelir-Gider Raporu',
                        html: `<div class='text-left mb-2'><b>Toplam Gelir:</b> ${data.income} ₺<br><b>Toplam Gider:</b> ${data.expense} ₺<br><b>Net Kar:</b> ${data.net} ₺</div>` + detailsTable,
                        width: 700,
                        showCloseButton: true,
                        confirmButtonText: 'Kapat',
                        customClass: {popup: 'p-4'}
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: data.message || 'Bir hata oluştu'
                    });
                }
            })
            .catch(error => {
            Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: error.message || 'Bir hata oluştu'
                });
            });
        }

        $(document).ready(function() {
            $('#reportMonth').select2({
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: -1 // arama kutusunu gizler
            });
        });
    </script>
</body>
</html> 