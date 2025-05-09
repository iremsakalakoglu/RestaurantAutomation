<div class="relative max-w-lg w-full mx-auto bg-white rounded-2xl shadow-2xl p-8 pt-10" style="min-width:340px;">
    <button onclick="closeAdisyonModal()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
        <i class="fas fa-times"></i>
    </button>

    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Masa {{ $order->table->name }}</h3>
        <p class="text-sm text-gray-600">Sipariş No: #{{ $order->id }}</p>
    </div>

    <div class="overflow-x-auto mb-6">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="py-2 px-2 text-left font-medium">Ürün</th>
                    <th class="py-2 px-2 text-center font-medium">Adet</th>
                    <th class="py-2 px-2 text-right font-medium">Fiyat</th>
                    <th class="py-2 px-2 text-right font-medium">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $detail)
                    <tr class="hover:bg-gray-50" id="order-detail-{{ $detail->id }}">
                        <td class="py-2 px-2 font-medium {{ $detail->is_paid ? 'text-green-600' : '' }}">
                            {{ $detail->product->name ?? '-' }}
                            @if($detail->is_paid)
                                <span class="text-xs ml-2">(Ödendi)</span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center {{ $detail->is_paid ? 'text-green-600' : '' }}">{{ $detail->quantity }}</td>
                        <td class="py-2 px-2 text-right {{ $detail->is_paid ? 'text-green-600' : '' }}">₺{{ number_format($detail->price * $detail->quantity, 2) }}</td>
                        <td class="py-2 px-2 text-right">
                            @if(!$detail->is_paid)
                                <button onclick="payOrderDetail({{ $detail->id }})" 
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-green-100 hover:bg-green-200 text-green-600 hover:text-green-700 transition-colors">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t">
                    <td colspan="4" class="py-4 text-right font-bold">
                        <span>Toplam: ₺{{ number_format($order->orderDetails->sum(function($detail) { return $detail->price * $detail->quantity; }), 2) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->payment)
        <div class="flex items-center gap-4 text-sm mb-6">
            <span class="font-semibold text-gray-700">Ödeme Yöntemi:</span>
            <span class="capitalize">{{ $order->payment->payment_method }}</span>
        </div>
    @endif

    <div class="flex justify-end">
        <button onclick="closeAdisyonModal()" class="text-[#d4a373] hover:text-[#b88b5a] font-medium text-sm flex items-center gap-2 px-4 py-2 rounded-lg border border-[#d4a373] hover:border-[#b88b5a] transition-colors">
            <i class="fa-solid fa-receipt"></i>
            Adisyonu Kapat
        </button>
    </div>
</div>
