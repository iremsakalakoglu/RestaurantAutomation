<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Central Perk - Masa Yönetimi</title>
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
                    <a href="{{ route('admin.tables') }}" class="flex items-center p-2 text-gray-700 bg-gray-100 rounded">
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
                    <a href="{{ route('admin.inventory') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
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
    <div class="ml-64 p-8 mt-16">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Masa Yönetimi</h1>
            <button onclick="openTableModal()" class="bg-[#d4a373] text-white px-4 py-2 rounded hover:bg-[#c48c63] transition-all">
                <i class="fas fa-plus mr-2"></i>Yeni Masa Ekle
            </button>
        </div>

        <!-- Masa Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-20">
            @foreach($tables as $table)
            <div class="bg-white rounded-lg shadow-md p-6 relative">
                <div class="absolute top-4 right-4 space-x-2">
                    <button onclick="editTable({{ $table->id }})" class="text-[#d4a373] hover:text-[#c48c63]">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteTable({{ $table->id }})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="flex items-center justify-center mb-4">
                    <i class="fas fa-chair text-4xl text-[#d4a373]"></i>
                </div>
                <h3 class="text-xl font-semibold text-center mb-2">Masa {{ $table->table_number }}</h3>
                <div class="text-center text-gray-600">
                    <p class="mb-2">
                        <i class="fas fa-users text-sm mr-1"></i>
                        <span>{{ $table->capacity ?? 4 }} Kişilik</span>
                    </p>
                    <p class="mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $table->status === 'boş' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $table->status === 'boş' ? 'Müsait' : 'Dolu' }}
                        </span>
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-user-tie text-sm mr-1"></i>
                        <span>{{ $table->waiter ? $table->waiter->name : 'Garson Atanmamış' }}</span>
                    </p>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('admin.tables.qrcode', $table->id) }}" class="flex items-center justify-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                        <i class="fas fa-qrcode mr-2"></i> QR Kodu Görüntüle
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Sayfalama -->
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-white/40 backdrop-blur-sm p-4 rounded-lg shadow-md w-auto z-10" style="font-size: 0.95rem;">
            <div class="flex justify-center">
                {{ $tables->links() }}
            </div>
        </div>
    </div>

    <!-- Masa Modal -->
    <div id="tableModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 max-w-md w-full">
            <h2 class="text-2xl font-bold mb-6" id="modalTitle">Yeni Masa Ekle</h2>
            <form id="tableForm" onsubmit="handleTableSubmit(event)">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Masa Numarası</label>
                            <input type="number" id="tableNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Masa Kapasitesi</label>
                    <input type="number" id="tableCapacity" class="w-full px-3 py-2 border border-gray-300 rounded-md" min="1" required>
                            <p class="mt-1 text-sm text-gray-500">Masada oturabilen maksimum kişi sayısı</p>
                        </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sorumlu Garson</label>
                    <select id="waiterId" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Garson Seçin</option>
                        @foreach($waiters as $waiter)
                            <option value="{{ $waiter->id }}">{{ $waiter->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeTableModal()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-all">
                        İptal
                    </button>
                    <button type="submit" id="submitButton"
                            class="px-4 py-2 bg-[#d4a373] text-white rounded hover:bg-[#c48c63] transition-all">
                        Kaydet
                    </button>
                        </div>
                    </form>
        </div>
    </div>

    <script>
    let editingTableId = null;
    
    function openTableModal(tableId = null) {
        editingTableId = tableId;
        const modal = document.getElementById('tableModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('tableForm');
        
        modalTitle.textContent = tableId ? 'Masa Düzenle' : 'Yeni Masa Ekle';
        form.reset();
        
        if (tableId) {
            // Masa bilgilerini getir
            fetch(`/admin/tables/${tableId}`)
                .then(response => response.json())
                .then(table => {
                    document.getElementById('tableNumber').value = table.table_number;
                    document.getElementById('tableCapacity').value = table.capacity || 4;
                    document.getElementById('waiterId').value = table.waiter_id || '';
                });
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeTableModal() {
        const modal = document.getElementById('tableModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        editingTableId = null;
    }
    
    function handleTableSubmit(event) {
        event.preventDefault();
        
        const tableNumber = document.getElementById('tableNumber').value;
        const capacity = document.getElementById('tableCapacity').value;
        const waiterId = document.getElementById('waiterId').value;
        
        const url = editingTableId 
            ? `/admin/tables/${editingTableId}`
            : '/admin/tables';
            
        const method = editingTableId ? 'PUT' : 'POST';
        
        fetch(url, {
            method: method,
                                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
            body: JSON.stringify({
                table_number: tableNumber,
                capacity: capacity,
                waiter_id: waiterId || null
            })
                            })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Başarılı!',
                    text: data.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                                    });
            } else {
                Swal.fire({
                    title: 'Hata!',
                    text: data.message,
                    icon: 'error'
                });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            Swal.fire({
                title: 'Hata!',
                text: 'Bir hata oluştu',
                icon: 'error'
                    });
                });
        }

        function deleteTable(id) {
            Swal.fire({
                title: 'Emin misiniz?',
            text: "Bu masa silinecek!",
                icon: 'warning',
                showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Evet, sil!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/tables/${id}`, {
                    method: 'DELETE',
                        headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                    })
                .then(response => response.json())
                    .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Silindi!',
                            data.message,
                            'success'
                        ).then(() => {
                            location.reload();
                    });
                    } else {
                        Swal.fire(
                            'Hata!',
                            data.message,
                            'error'
                        );
                    }
                    });
                }
            });
        }

        function editTable(id) {
            openTableModal(id);
        }
    </script>

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
</body>
</html> 