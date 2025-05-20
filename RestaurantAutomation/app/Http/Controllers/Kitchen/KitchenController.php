<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KitchenController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (Auth::user()->role !== 'kitchen') {
            return redirect()->route('menu')->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Bekleyen siparişleri getir (sipariş alındı durumundakiler)
        $pendingOrders = Order::where('status', 'sipariş alındı')
            ->with(['table', 'orderDetails.product'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Hazırlanan siparişleri getir (hazırlanıyor durumundakiler)
        $preparingOrders = Order::where('status', 'hazırlanıyor')
            ->with(['table', 'orderDetails.product'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Tamamlanmış siparişleri getir (hazır ve teslim edildi durumundakiler)
        $today = Carbon::now('Europe/Istanbul')->startOfDay();
        $completedOrders = Order::whereIn('status', ['hazır', 'teslim edildi'])
            ->with(['table', 'orderDetails.product'])
            ->where('updated_at', '>=', $today)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Bugün tamamlanan siparişleri say
        $completedToday = $completedOrders->count();

        // Tüm siparişleri birleştir
        $allOrders = $pendingOrders->concat($preparingOrders)->concat($completedOrders);

        return view('kitchen.kitchen', [
            'pendingOrders' => $pendingOrders,
            'preparingOrders' => $preparingOrders,
            'completedOrders' => $completedOrders,
            'completedToday' => $completedToday,
            'activeOrders' => $allOrders
        ]);
    }

    // Tüm siparişleri listele
    public function orders()
    {
        $orders = Order::orderBy('created_at', 'desc')->paginate(10);
        return view('kitchen.orders', [
            'orders' => $orders
        ]);
    }

    // Belirli bir siparişin detaylarını göster
    public function showOrder($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        return view('kitchen.order-detail', [
            'order' => $order
        ]);
    }

    // Sipariş durumunu güncelle
    public function updateOrderStatus(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'kitchen') {
            return response()->json([
                'success' => false,
                'message' => 'Yetkisiz erişim'
            ], 403);
        }

        try {
            \Log::info('Mutfak: Sipariş durumu güncelleme isteği', [
                'order_id' => $id,
                'status' => $request->status
            ]);
            
            // Gelen durumu kontrol et
            if (!in_array($request->status, ['hazırlanıyor', 'hazır'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz sipariş durumu'
                ], 400);
            }

            // API üzerinden durumu güncelle
            $response = app(\App\Http\Controllers\Api\OrderController::class)
                ->update(new Request(['status' => $request->status]), $id);
            
            \Log::info('Mutfak: API yanıtı', [
                'order_id' => $id,
                'response' => json_decode($response->getContent(), true)
            ]);
            
            // Bildirim ekle (hızlı çözüm)
            $order = Order::findOrFail($id);
            if ($order->customer_id) {
                $message = null;
                switch ($request->status) {
                    case 'hazırlanıyor':
                        $message = 'Siparişiniz hazırlanıyor.';
                        break;
                    case 'hazır':
                        $message = 'Siparişiniz hazırlandı.';
                        break;
                }
                if ($message) {
                    try {
                        $notification = \App\Models\Notification::create([
                            'order_id' => $order->id,
                            'customer_id' => $order->customer_id,
                            'type' => 'sipariş',
                            'message' => $message,
                            'status' => 'okunmadı'
                        ]);
                        \Log::info('KITCHEN: Notification başarıyla oluşturuldu', ['notification' => $notification]);
                    } catch (\Exception $e) {
                        \Log::error('KITCHEN: Notification oluşturulamadı', ['error' => $e->getMessage()]);
                    }
                }
            }
            
            return $response;
            
        } catch (\Exception $e) {
            \Log::error('Mutfak: Sipariş durumu güncelleme hatası', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ürün durumunu güncelle
    public function updateItemStatus(Request $request, $orderId, $itemId)
    {
        try {
            if (!Auth::check() || Auth::user()->role !== 'kitchen') {
                throw new \Exception('Yetkisiz erişim');
            }

            \Log::info('Ürün durumu güncelleme isteği:', [
                'order_id' => $orderId,
                'item_id' => $itemId,
                'is_ready' => $request->input('is_ready')
            ]);

            $order = Order::findOrFail($orderId);
            
            // Sipariş detayını bul
            $orderDetail = $order->orderDetails()->where('id', $itemId)->first();
            
            if (!$orderDetail) {
                throw new \Exception('Sipariş detayı bulunamadı');
            }

            // is_ready değerini güncelle
            $orderDetail->is_ready = $request->boolean('is_ready');
            $orderDetail->save();

            // Tüm ürünler hazır mı kontrol et
            $allItemsReady = !$order->orderDetails()
                ->where('is_ready', false)
                ->exists();

            // Eğer tüm ürünler hazırsa siparişi hazır olarak işaretle
            if ($allItemsReady) {
                // API üzerinden durumu güncelle
                app(\App\Http\Controllers\Api\OrderController::class)
                    ->update(new Request(['status' => 'hazır']), $orderId);
            }

            \Log::info('Ürün durumu başarıyla güncellendi', [
                'order_id' => $orderId,
                'item_id' => $itemId,
                'all_items_ready' => $allItemsReady
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ürün durumu güncellendi',
                'allItemsReady' => $allItemsReady
            ]);

        } catch (\Exception $e) {
            \Log::error('Ürün durumu güncelleme hatası', [
                'order_id' => $orderId,
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    // Ürün iptal etme
    public function cancelItem($orderId, $itemId)
    {
        try {
            if (!\Auth::check() || \Auth::user()->role !== 'kitchen') {
                throw new \Exception('Yetkisiz erişim');
            }

            $order = \App\Models\Order::findOrFail($orderId);
            $orderDetail = $order->orderDetails()->where('id', $itemId)->first();
            if (!$orderDetail) {
                throw new \Exception('Sipariş detayı bulunamadı');
            }

            $orderDetail->is_canceled = true;
            $orderDetail->save();

            // Eğer siparişte hazır olmayan ve iptal edilmeyen ürün kalmadıysa siparişi hazır yap
            $hazirOlmayanUrunVarMi = $order->orderDetails()
                ->where('is_ready', false)
                ->where('is_canceled', false)
                ->exists();
            if (!$hazirOlmayanUrunVarMi) {
                $order->status = 'hazır';
                $order->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Ürün iptal edildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
