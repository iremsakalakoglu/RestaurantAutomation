<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name ?? 'Restaurant' }} - Destek Talebi Detay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
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
    <div class="fixed left-0 top-16 h-full w-64 bg-white shadow-md">
        <div class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-100' : '' }} rounded">
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
                    <a href="{{ route('admin.tables') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded {{ request()->routeIs('admin.tables') ? 'bg-gray-100' : '' }}">
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
                    <a href="{{ route('admin.settings') }}" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded">
                        <i class="fas fa-cog w-6"></i>
                        <span>Ayarlar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="ml-64 mt-16 p-8">
        <a href="{{ route('admin.support-messages') }}" class="text-[#b88b5a] hover:underline mb-4 inline-block"><i class="fas fa-arrow-left mr-2"></i>Geri Dön</a>
        <h1 class="text-3xl font-bold text-[#b88b5a] mb-8">Destek Talebi Detay</h1>
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><span class="font-semibold">Ad Soyad:</span> {{ $message->fullname }}</div>
                <div><span class="font-semibold">Telefon:</span> {{ $message->phone ?: '-' }}</div>
                <div><span class="font-semibold">E-posta:</span> {{ $message->email }}</div>
                <div><span class="font-semibold">Konu:</span> {{ $message->subject }}</div>
                <div><span class="font-semibold">Tarih:</span> {{ $message->created_at->format('d.m.Y H:i') }}</div>
                <div class="col-span-1 md:col-span-2">
                    <span class="font-semibold">Mesaj:</span>
                    <div class="mt-2 flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 text-blue-500 flex items-center justify-center rounded-full mr-3">
                            <i class="fas fa-comment-dots text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="bg-blue-50 border-l-4 border-blue-400 rounded-lg shadow-sm p-4">
                                <div class="font-semibold text-blue-700 mb-1">Gönderilen Mesaj</div>
                                <div class="text-gray-800">{{ $message->message }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(!$message->admin_reply)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Cevapla</h2>
            <form action="{{ route('admin.support-messages.reply', $message->id) }}" method="POST">
                @csrf
                <textarea name="admin_reply" rows="4" class="w-full border rounded p-2 mb-4" placeholder="Cevabınızı yazınız...">{{ old('admin_reply', $message->admin_reply) }}</textarea>
                @error('admin_reply')
                    <div class="text-red-500 mb-2">{{ $message }}</div>
                @enderror
                <button type="submit" class="bg-[#b88b5a] text-white px-6 py-2 rounded hover:bg-[#a0764b] transition">Cevabı Kaydet</button>
            </form>
        </div>
        @endif
        @if($message->admin_reply)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 relative">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-green-700">Verilen Cevap:</h3>
                <button onclick="document.getElementById('edit-reply-form').classList.toggle('hidden')" class="text-green-700 hover:text-green-900 text-xs px-2 py-1 rounded transition border border-green-200 bg-green-100 ml-2">
                    <i class="fas fa-edit mr-1"></i>Düzenle
                </button>
            </div>
            <div class="text-green-900 mb-2">{{ $message->admin_reply }}</div>
            <form id="edit-reply-form" action="{{ route('admin.support-messages.reply', $message->id) }}" method="POST" class="hidden mt-2">
                @csrf
                <textarea name="admin_reply" rows="3" class="w-full border rounded p-2 mb-2">{{ $message->admin_reply }}</textarea>
                <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">Kaydet</button>
            </form>
        </div>
        @endif
    </div>
</body>
</html> 