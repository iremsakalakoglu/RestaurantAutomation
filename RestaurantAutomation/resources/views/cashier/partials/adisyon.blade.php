<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="relative max-w-3xl w-full mx-auto bg-white rounded-2xl shadow-2xl" style="min-width:600px; max-height: 90vh; display: flex; flex-direction: column;" data-table-id="{{ $order->table->id }}">
    <!-- Kapatma Butonu -->
    <button class="absolute top-4 right-6 text-gray-400 hover:text-gray-600 text-2xl transition-colors duration-200" 
            onclick="closeAdisyonModal()" 
            aria-label="Kapat">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- Başlık -->
    <div class="flex-shrink-0 p-8 pb-0">
        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-200">
            <div class="bg-[#d4a373] text-white p-3 rounded-lg">
                <i class="fa-solid fa-receipt text-3xl"></i>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 tracking-tight mb-1">Masa {{ $order->table->table_number ?? '-' }}</h3>
                <div class="flex gap-4 text-gray-600">
                    @if(isset($orders) && $orders->count() > 1)
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $orders->count() }} Sipariş
                        </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <i class="fa-regular fa-clock"></i>
                        {{ $orders->first()->created_at->format('H:i') }} - {{ $orders->last()->created_at->format('H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sipariş Detayları - Kaydırılabilir Alan -->
    <div class="flex-grow overflow-y-auto p-8 pt-0">
        <div class="overflow-x-auto">
            <table class="w-full text-gray-700">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="py-3 text-left text-base w-2/5">Ürün</th>
                        <th class="py-3 text-center text-base w-1/5">Adet</th>
                        <th class="py-3 text-right text-base w-1/5">Birim Fiyat</th>
                        <th class="py-3 text-right text-base w-1/5">Tutar</th>
                        <th class="py-3 text-center text-base w-24">Durum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->orderDetails as $detail)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" id="order-detail-{{ $detail->id }}">
                            <td class="py-4 px-2 font-medium text-base">
                                {{ $detail->product->name ?? '-' }}
                                @if(isset($orders) && $orders->count() > 1)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Sipariş #{{ $detail->order_id }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-2 text-center text-base">
                                @if(!$detail->is_paid)
                                    <div class="flex items-center justify-center gap-2">
                                        <input type="number" 
                                               id="pay-quantity-{{ $detail->id }}"
                                               class="w-20 px-2 py-1 border rounded text-center" 
                                               min="1" 
                                               max="{{ $detail->quantity }}" 
                                               value="{{ $detail->quantity }}"
                                               onchange="updatePaymentAmount({{ $detail->id }}, {{ $detail->price }}, this.value)">
                                        <span class="text-gray-500">/ {{ $detail->quantity }}</span>
                                    </div>
                                @else
                                    {{ $detail->quantity }}
                                @endif
                            </td>
                            <td class="py-4 px-2 text-right text-base whitespace-nowrap">₺{{ number_format($detail->price, 2) }}</td>
                            <td class="py-4 px-2 text-right font-medium text-base whitespace-nowrap" id="total-amount-{{ $detail->id }}">₺{{ number_format($detail->price * $detail->quantity, 2) }}</td>
                            <td class="py-4 px-2 text-center">
                                @if(!$detail->is_paid)
                                    <button type="button" 
                                        onclick="handlePayment({{ $detail->id }})" 
                                        class="w-10 h-10 rounded-full bg-green-100 hover:bg-green-200 text-green-600 flex items-center justify-center transition-all duration-200 hover:scale-105">
                                        <i class="fas fa-check text-base"></i>
                                    </button>
                                @else
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-green-100 text-green-600 text-sm whitespace-nowrap">
                                        Ödendi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alt Kısım - Sabit -->
    <div class="flex-shrink-0 p-8 pt-0">
        <!-- Toplam ve Ödeme Durumu -->
        <div class="bg-gray-50 rounded-xl p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xl font-semibold text-gray-700">Toplam Tutar:</span>
                <span class="text-2xl font-bold text-gray-800">
                    ₺{{ number_format($order->orderDetails->sum(fn($d) => $d->price * $d->quantity), 2) }}
                </span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="font-medium text-gray-700">Durum:</span>
                    @if($order->payment_status == 'ödendi')
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 text-green-700 font-medium">
                            <i class="fa-solid fa-check-circle"></i> Ödendi
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-yellow-100 text-yellow-700 font-medium">
                            <i class="fa-solid fa-clock"></i> Bekliyor
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tümünü Öde Butonu -->
        @if($order->orderDetails->where('is_paid', false)->count() > 0)
            <div class="flex justify-end">
                <button type="button" 
                    onclick="handlePaymentAll({{ $order->id }})"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl text-lg font-semibold flex items-center gap-3 shadow-lg transition-all duration-200 hover:scale-105">
                    <i class="fa-solid fa-money-bill-wave"></i> 
                    Tümünü Öde
                </button>
            </div>
        @endif
    </div>
</div>
