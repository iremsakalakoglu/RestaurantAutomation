<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name ?? 'Restaurant' }}  - Destek Talepleri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        <h1 class="text-3xl font-bold text-[#b88b5a] mb-8">Destek Talepleri</h1>
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end w-full">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Ad Soyad</label>
                    <div class="relative">
                        <span class="absolute left-2 top-2.5 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" name="fullname" value="{{ request('fullname') }}" class="border rounded px-8 py-2 w-full focus:ring-2 focus:ring-[#b88b5a]" placeholder="Ad Soyad">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Konu</label>
                    <select name="subject" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-[#b88b5a]">
                        <option value="">Tüm Konular</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject }}" @if(request('subject') == $subject) selected @endif>{{ $subject }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Tarih Aralığı</label>
                    <input id="date_range" type="text" class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-[#b88b5a]" placeholder="Tarih aralığı seçin" readonly>
                    <input type="hidden" name="date_start" id="date_start" value="{{ request('date_start') }}">
                    <input type="hidden" name="date_end" id="date_end" value="{{ request('date_end') }}">
                </div>
                <div class="flex gap-2 mt-4 md:mt-0 justify-end">
                    <a href="{{ route('admin.support-messages') }}" class="flex items-center gap-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded transition text-base"><i class="fas fa-times"></i> Temizle</a>
                    <button type="submit" class="flex items-center gap-1 bg-[#b88b5a] hover:bg-[#a0764b] text-white px-6 py-3 rounded transition text-base"><i class="fas fa-filter"></i> Filtrele</button>
                </div>
            </form>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ad Soyad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">E-posta</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Konu</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mesaj</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messages as $msg)
                    <tr>
                        <td class="px-4 py-2">{{ $msg->fullname }}</td>
                        <td class="px-4 py-2">{{ $msg->phone }}</td>
                        <td class="px-4 py-2">{{ $msg->email }}</td>
                        <td class="px-4 py-2">{{ $msg->subject }}</td>
                        <td class="px-4 py-2 max-w-xs truncate" title="{{ $msg->message }}">{{ Str::limit($msg->message, 50) }}</td>
                        <td class="px-4 py-2">{{ $msg->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.support-messages.show', $msg->id) }}"
                               class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold hover:bg-blue-200 transition">
                                Detay / Cevapla
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">Henüz destek talebi yok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $messages->links() }}</div>
        </div>
    </div>
    <!-- Flatpickr JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: ["{{ request('date_start') }}", "{{ request('date_end') }}"],
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById('date_start').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                    document.getElementById('date_end').value = instance.formatDate(selectedDates[1], 'Y-m-d');
                } else if (selectedDates.length === 1) {
                    document.getElementById('date_start').value = instance.formatDate(selectedDates[0], 'Y-m-d');
                    document.getElementById('date_end').value = '';
                } else {
                    document.getElementById('date_start').value = '';
                    document.getElementById('date_end').value = '';
                }
            }
        });
    </script>
</body>
</html>
