<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Masa QR Kodu</title>
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
    <div class="ml-64 pt-16 p-10">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-700">Masa QR Kodu</h1>
            <a href="{{ route('admin.tables') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Geri Dön
            </a>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4">Masa {{ $table->table_number }}</h2>
                <p class="text-gray-600 mb-6">Bu QR kodu taratarak müşteriler doğrudan bu masa için menüye erişebilirler.</p>
                
                <div class="flex justify-center mb-8">
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <img src="{{ route('qrcode.generate', $table->id) }}" alt="QR Code" class="mx-auto">
                    </div>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <button onclick="printQRCode()" class="bg-[#d4a373] hover:bg-[#c48c63] text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-print mr-2"></i> QR Kodu Yazdır
                    </button>
                    <a href="{{ route('qrcode.generate', $table->id) }}" download="masa_{{ $table->table_number }}_qr.svg" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded flex items-center">
                        <i class="fas fa-download mr-2"></i> QR Kodu İndir
                    </a>
                </div>

                <div class="mt-8 text-sm text-gray-500">
                    <p>QR kodunu taratıldığında şu URL'ye yönlendirilecektir:</p>
                    <code class="bg-gray-100 px-2 py-1 rounded">{{ config('app.url') }}/menu?table={{ $table->id }}</code>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printQRCode() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Masa ${table.table_number} QR Kodu</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; }
                        .qr-container { margin: 20px auto; padding: 10px; }
                        h2 { margin-bottom: 10px; }
                    </style>
                </head>
                <body>
                    <div class="qr-container">
                        <h2>Masa ${table.table_number}</h2>
                        <img src="${route('qrcode.generate', $table->id)}" style="width: 300px; height: 300px;">
                        <p>Sipariş vermek için QR kodu taratın</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
</body>
</html> 