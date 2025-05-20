<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Table;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (Auth::user()->role !== 'cashier') {
            return redirect()->route('menu')->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Ödeme bekleyen siparişler (teslim edilmiş ama ödenmemiş)
        $pendingOrders = Order::where('status', 'teslim edildi')
            ->where('payment_status', '!=', 'ödendi')
            ->count();

        // Bugün ödemesi alınanlar
        $todayPayments = OrderDetail::whereHas('order', function($query) {
            $query->whereDate('updated_at', today())
                ->where('payment_status', 'ödendi');
        })->where('is_paid', true)->count();

        // Günlük ciro
        $dailyRevenue = OrderDetail::whereHas('order', function($query) {
            $query->whereDate('updated_at', today())
                ->where('payment_status', 'ödendi');
        })->where('is_paid', true)
        ->sum(DB::raw('price * quantity'));

        // Tüm masaları garson ve aktif siparişiyle birlikte çek
        $tables = Table::with(['waiter', 'currentOrder'])->get();

        // Garsonun tüm ürünleri teslim ettiği siparişler (teslim edildi ve tüm orderDetails is_delivered = true)
        $deliveredOrders = Order::where('status', 'teslim edildi')
            ->whereDoesntHave('orderDetails', function($query) {
                $query->where('is_delivered', false);
            })
            ->with(['table', 'orderDetails.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.cashier', [
            'pendingOrders' => $pendingOrders,
            'todayPayments' => $todayPayments,
            'todayPaymentsAmount' => $dailyRevenue,
            'tables' => $tables,
            'deliveredOrders' => $deliveredOrders
        ]);
    }

    public function showOrder($id)
    {
        $order = Order::with(['orderDetails.product'])->findOrFail($id);
        return view('cashier.order-detail', [
            'order' => $order
        ]);
    }

    public function processPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $request->validate([
            'payment_method' => 'required|in:nakit,kredi_karti',
            'amount' => 'required|numeric|min:0'
        ]);

        // Create payment record
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->amount = $request->amount;
        $payment->payment_method = $request->payment_method;
        $payment->status = 'tamamlandı';
        $payment->processed_by = Auth::id();
        $payment->save();

        // Update order status
        $order->status = 'teslim edildi';
        $order->save();

        // If this was a table order, free up the table
        if ($order->table_id) {
            $order->table->status = 'boş';
            $order->table->save();
        }

        return redirect()->route('cashier.dashboard')->with('success', 'Ödeme başarıyla tamamlandı');
    }

    // Adisyon modalı için fonksiyon
    public function adisyon($tableId)
    {
        // Önce masanın durumunu kontrol et
        $table = Table::findOrFail($tableId);
        
        if ($table->status === 'boş') {
            return response('
                <div class="relative max-w-3xl w-full mx-auto bg-white rounded-2xl shadow-2xl p-8" style="min-width:600px;">
                    <!-- Kapatma Butonu -->
                    <button class="absolute top-4 right-6 text-gray-400 hover:text-gray-600 text-2xl transition-colors duration-200" 
                            onclick="closeAdisyonModal()" 
                            aria-label="Kapat">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="text-6xl text-gray-300 mb-6">
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-600 mb-2">Masa Boş</h3>
                        <p class="text-gray-500">Bu masada aktif sipariş bulunmuyor.</p>
                    </div>
                </div>
            ', 200);
        }

        // Masaya ait tüm ödeme bekleyen siparişleri bul
        $ordersQuery = Order::with(['orderDetails.product', 'table', 'payment'])
            ->where('table_id', $tableId)
            ->where('status', 'teslim edildi')
            ->whereDoesntHave('orderDetails', function($query) {
                $query->where('is_delivered', false);
            });

        // Sadece son masa durum değişikliğinden sonraki siparişleri al
        if ($table->status_changed_at) {
            $ordersQuery->where('created_at', '>', $table->status_changed_at);
        }

        $orders = $ordersQuery->orderBy('created_at', 'desc')->get();

        if ($orders->isEmpty()) {
            return response('
                <div class="relative max-w-3xl w-full mx-auto bg-white rounded-2xl shadow-2xl p-8" style="min-width:600px;">
                    <!-- Kapatma Butonu -->
                    <button class="absolute top-4 right-6 text-gray-400 hover:text-gray-600 text-2xl transition-colors duration-200" 
                            onclick="closeAdisyonModal()" 
                            aria-label="Kapat">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <div class="flex flex-col items-center justify-center py-12">
                        <div class="text-6xl text-gray-300 mb-6">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-600 mb-2">Bu Masada Adisyon Bulunmuyor</h3>
                        <p class="text-gray-500">Masada aktif bir sipariş veya ödeme bekleyen adisyon yok.</p>
                    </div>
                </div>
            ', 200);
        }

        // Tüm siparişlerin detaylarını birleştir
        $combinedOrderDetails = collect();
        foreach ($orders as $order) {
            $combinedOrderDetails = $combinedOrderDetails->concat($order->orderDetails);
        }

        // İlk siparişi temel al ama tüm detayları birleştirilmiş halde gönder
        $mainOrder = $orders->first();
        $mainOrder->orderDetails = $combinedOrderDetails;

        return view('cashier.partials.adisyon', [
            'order' => $mainOrder,
            'orders' => $orders // Tüm siparişleri de gönder
        ]);
    }

    public function payOrderDetail($detailId)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($detailId);
            
            // Request'ten ödenecek miktarı al
            $request = request();
            $data = json_decode($request->getContent(), true) ?? [];
            
            \Log::info('Ödeme isteği alındı', [
                'detail_id' => $detailId,
                'request_data' => $data,
                'content_type' => $request->header('Content-Type')
            ]);
            
            $payQuantity = isset($data['quantity']) ? (int)$data['quantity'] : $orderDetail->quantity;
            
            // Geçerli miktar kontrolü
            if ($payQuantity <= 0 || $payQuantity > $orderDetail->quantity) {
                \Log::warning('Geçersiz ödeme miktarı', [
                    'detail_id' => $detailId,
                    'pay_quantity' => $payQuantity,
                    'order_quantity' => $orderDetail->quantity
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz ödeme miktarı.'
                ], 400);
            }

            // Ürünün ödenip ödenmediğini kontrol et
            if ($orderDetail->is_paid) {
                \Log::warning('Ürün zaten ödenmiş', [
                    'detail_id' => $detailId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün zaten ödenmiş.'
                ], 400);
            }

            DB::beginTransaction();
            try {
                if ($payQuantity < $orderDetail->quantity) {
                    // Kısmi ödeme: Yeni bir OrderDetail oluştur
                    $remainingQuantity = $orderDetail->quantity - $payQuantity;
                    
                    // Ödenen miktar için mevcut OrderDetail'i güncelle
                    $orderDetail->quantity = $payQuantity;
                    $orderDetail->is_paid = true;
                    $orderDetail->save();
                    
                    // Kalan miktar için yeni OrderDetail oluştur
                    $newOrderDetail = $orderDetail->replicate();
                    $newOrderDetail->quantity = $remainingQuantity;
                    $newOrderDetail->is_paid = false;
                    $newOrderDetail->save();
                    
                    \Log::info('Kısmi ödeme alındı', [
                        'detail_id' => $detailId,
                        'paid_quantity' => $payQuantity,
                        'remaining_quantity' => $remainingQuantity
                    ]);
                } else {
                    // Tam ödeme
                    $orderDetail->is_paid = true;
                    $orderDetail->save();
                    
                    \Log::info('Tam ödeme alındı', [
                        'detail_id' => $detailId
                    ]);
                }

                // Tüm ürünler ödendiyse siparişi tamamla
                $allPaid = $orderDetail->order->orderDetails()
                    ->where('is_paid', false)
                    ->doesntExist();

                if ($allPaid) {
                    $orderDetail->order->status = 'teslim edildi';
                    $orderDetail->order->payment_status = 'ödendi';
                    $orderDetail->order->save();

                    \Log::info('Sipariş tamamlandı ve ödendi', [
                        'order_id' => $orderDetail->order->id
                    ]);
                }

                DB::commit();

                // Yeni toplam tutarı hesapla
                $newTotal = $orderDetail->order->orderDetails()
                    ->where('is_paid', false)
                    ->sum(DB::raw('price * quantity'));

                return response()->json([
                    'success' => true,
                    'message' => 'Ürün ödemesi alındı.',
                    'newTotal' => number_format($newTotal, 2)
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Ödeme işlemi sırasında hata', [
                'detail_id' => $detailId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function payAllOrderDetails(Request $request, $orderId)
    {
        try {
            DB::beginTransaction();

            $order = Order::with('orderDetails')->findOrFail($orderId);
            
            // Tüm ödenmemiş detayları ödenmiş olarak işaretle
            foreach($order->orderDetails()->where('is_paid', false)->get() as $detail) {
                $detail->is_paid = true;
                $detail->save();
            }

            // Siparişi tamamlandı ve ödenmiş olarak işaretle
            $order->status = 'teslim edildi';
            $order->payment_status = 'ödendi';
            $order->save();

            \Log::info('Tüm ödemeler tamamlandı', [
                'order_id' => $order->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tüm ödemeler başarıyla alındı.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Toplu ödeme işlemi sırasında hata', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function payAll($orderId)
    {
        try {
            DB::beginTransaction();

            // Ana siparişi bul
            $mainOrder = Order::findOrFail($orderId);
            
            // Aynı masaya ait tüm ödenmemiş siparişleri bul
            $orders = Order::where('table_id', $mainOrder->table_id)
                ->where('status', 'teslim edildi')
                ->whereDoesntHave('orderDetails', function($query) {
                    $query->where('is_delivered', false);
                })
                ->get();

            $totalAmount = 0;

            // Tüm siparişlerin detaylarını ödenmiş olarak işaretle
            foreach ($orders as $order) {
                foreach ($order->orderDetails as $detail) {
                    if (!$detail->is_paid) {
                        $detail->is_paid = true;
                        $detail->save();
                        $totalAmount += $detail->price * $detail->quantity;
                    }
                }

                // Her siparişi tamamlandı olarak işaretle
                $order->status = 'teslim edildi';
                $order->payment_status='ödendi';
                $order->save();

                // Her sipariş için ödeme kaydı oluştur
                Payment::create([
                    'order_id' => $order->id,
                    'table_id' => $mainOrder->table_id,
                    'amount' => $order->orderDetails->sum(fn($d) => $d->price * $d->quantity),
                    'payment_method' => 'nakit', // Varsayılan olarak nakit
                    'status' => 'tamamlandı',
                    'processed_by' => Auth::id()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tüm ödemeler başarıyla alındı.',
                'total' => $totalAmount
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Toplu ödeme hatası:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSummary()
    {
        // Ödeme bekleyen siparişler (teslim edilmiş ama ödenmemiş)
        $pendingOrders = Order::where('status', 'teslim edildi')
            ->where('payment_status', '!=', 'ödendi')
            ->count();

        // Bugün ödemesi alınanlar
        $todayPayments = OrderDetail::whereHas('order', function($query) {
            $query->whereDate('updated_at', today())
                ->where('payment_status', 'ödendi');
        })->where('is_paid', true)->count();

        // Günlük ciro
        $dailyRevenue = OrderDetail::whereHas('order', function($query) {
            $query->whereDate('updated_at', today())
                ->where('payment_status', 'ödendi');
        })->where('is_paid', true)
        ->sum(DB::raw('price * quantity'));

        return response()->json([
            'pendingOrders' => $pendingOrders,
            'todayPayments' => $todayPayments,
            'dailyRevenue' => number_format($dailyRevenue, 2)
        ]);
    }
}