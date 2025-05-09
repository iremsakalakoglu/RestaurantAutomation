<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function show($id)
    {
        try {
            $order = Order::with(['customer', 'table', 'orderDetails.product'])
                ->findOrFail($id);
            
            // Sipariş detaylarını zenginleştir
            $orderDetails = [];
            foreach ($order->orderDetails as $detail) {
                $detailData = $detail->toArray();
                
                // Ürün adını ekle
                if ($detail->product) {
                    $detailData['product_name'] = $detail->product->name;
                } else {
                    $detailData['product_name'] = "Ürün #" . $detail->product_id;
                }
                
                // Sayısal değerleri doğru tiplerde olduğundan emin ol
                $detailData['price'] = (float) $detail->price;
                $detailData['quantity'] = (int) $detail->quantity;
                
                $orderDetails[] = $detailData;
            }
            
            // Dönüş veri modelini oluştur
            $responseData = $order->toArray();
            $responseData['order_details'] = $orderDetails;
            
            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Sipariş detayları alınırken hata oluştu', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sipariş detayları alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            \Log::info('DEBUG: Api\\OrderController@update tetiklendi', ['order_id' => $id, 'status' => $request->status]);
            \Log::info('Sipariş güncelleme isteği alındı', [
                'order_id' => $id,
                'status' => $request->status,
                'request_data' => $request->all()
            ]);
            
            $request->validate([
                'status' => 'required|string'
            ]);
            
            DB::beginTransaction();
            
            $order = Order::with('orderDetails.product')->findOrFail($id);
            $oldStatus = $order->status;
            
            $status = $request->input('status');
            $dbStatus = $this->mapStatusToDb($status);
            
            \Log::info('Sipariş durumu değişikliği', [
                'order_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $dbStatus,
                'order_details' => $order->orderDetails->toArray()
            ]);
            
            $order->status = $dbStatus;
            
            // Eğer sipariş hazırlanıyor durumuna geçtiyse ve daha önce stok düşülmediyse stok düşürelim
            if ($dbStatus === 'hazırlanıyor' && $oldStatus === 'sipariş alındı') {
                \Log::info('Stok düşme koşulu sağlandı', [
                'order_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $dbStatus
            ]);
            
                try {
                $this->decreaseStock($order);
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Stok düşme işlemi başarısız', [
                        'order_id' => $id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }
            
            $order->save();

            \Log::info('DEBUG: customer_id kontrol', ['customer_id' => $order->customer_id]);
            // Bildirim ekle
            if ($order->customer_id) {
                $message = null;
                switch ($dbStatus) {
                    case 'hazırlanıyor':
                        $message = 'Siparişiniz hazırlanıyor.';
                        break;
                    case 'hazır':
                        $message = 'Siparişiniz hazırlandı.';
                        break;
                    case 'teslim':
                    case 'teslim edildi':
                        $message = 'Siparişiniz teslim edildi.';
                        break;
                    case 'bekliyor':
                    case 'sipariş alındı':
                        $message = 'Siparişiniz alındı.';
                        break;
                    case 'iptal':
                    case 'iptal edildi':
                        $message = 'Siparişiniz iptal edildi.';
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
                        \Log::info('API: Notification başarıyla oluşturuldu', ['notification' => $notification]);
                    } catch (\Exception $e) {
                        \Log::error('API: Notification oluşturulamadı', ['error' => $e->getMessage()]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sipariş durumu başarıyla güncellendi'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sipariş güncelleme hatası', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sipariş durumu güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sipariş ürünlerinin stoklarını azalt
     */
    private function decreaseStock(Order $order)
    {
        try {
            \Log::info('Stok düşme işlemi başlatılıyor', [
                'order_id' => $order->id,
                'order_details_count' => $order->orderDetails->count(),
                'order_details' => $order->orderDetails->toArray()
            ]);
            
            if ($order->orderDetails->isEmpty()) {
                throw new \Exception("Sipariş detayları bulunamadı");
            }
            
            foreach ($order->orderDetails as $detail) {
                \Log::info('Sipariş detayı işleniyor', [
                    'detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'quantity' => $detail->quantity
                ]);
                
                $stock = Stock::where('product_id', $detail->product_id)->first();
                
                if (!$stock) {
                    throw new \Exception("Ürün ID #{$detail->product_id} için stok bulunamadı");
                }
                
                $oldQuantity = $stock->quantity;
                
                // Stok miktarı yeterli mi kontrol et
                if ($stock->quantity < $detail->quantity) {
                    throw new \Exception("Ürün ID #{$detail->product_id} için yeterli stok bulunmuyor. Mevcut stok: {$stock->quantity}, İstenen: {$detail->quantity}");
                }
                
                // Stok miktarını güncelle
                $stock->quantity -= $detail->quantity;
                
                // Stok 0'ın altına düşemez
                if ($stock->quantity < 0) {
                    $stock->quantity = 0;
                }
                
                $stock->save();
                
                \Log::info('Stok miktarı güncellendi', [
                    'product_id' => $detail->product_id,
                    'old_quantity' => $oldQuantity,
                    'decrease_amount' => $detail->quantity,
                    'new_quantity' => $stock->quantity
                ]);
                
                try {
                // Stok hareketi oluştur
                    $stockMovement = StockMovement::create([
                    'stock_id' => $stock->id,
                        'order_id' => $order->id,
                        'type' => 'cikis',
                    'quantity' => $detail->quantity,
                    'description' => "Sipariş #" . $order->id . " için stok düşüldü",
                        'sale_price' => $stock->sale_price
                    ]);
                    
                    \Log::info('Stok hareketi oluşturuldu', [
                        'movement_id' => $stockMovement->id,
                        'stock_id' => $stock->id,
                        'order_id' => $order->id,
                        'type' => 'cikis',
                        'quantity' => $detail->quantity
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Stok hareketi oluşturulurken hata', [
                        'stock_id' => $stock->id,
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }
            
            \Log::info('Stok düşme işlemi başarıyla tamamlandı', ['order_id' => $order->id]);
            
        } catch (\Exception $e) {
            \Log::error('Stok düşme hatası', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * İptal edilen siparişin stoklarını geri ekle
     */
    private function increaseStock(Order $order)
    {
        try {
            \Log::info('Stok iade işlemi başlatılıyor', ['order_id' => $order->id]);
            
            foreach ($order->orderDetails as $detail) {
                $stock = Stock::where('product_id', $detail->product_id)->first();
                
                if (!$stock) {
                    throw new \Exception("Ürün ID #{$detail->product_id} için stok bulunamadı");
                }
                
                \Log::info('Stok iade edilecek ürün bilgileri', [
                    'product_id' => $detail->product_id,
                    'current_stock' => $stock->quantity,
                    'increase_amount' => $detail->quantity
                ]);
                
                // Stok miktarını güncelle
                $stock->quantity += $detail->quantity;
                $stock->save();
                
                \Log::info('Stok miktarı güncellendi', [
                    'product_id' => $detail->product_id,
                    'new_stock' => $stock->quantity
                ]);
                
                // Stok hareketi oluştur
                $stockMovement = StockMovement::create([
                    'stock_id' => $stock->id,
                    'order_id' => $order->id,
                    'type' => 'giris',
                    'quantity' => $detail->quantity,
                    'description' => "İptal edilen sipariş #" . $order->id . " için stok iade edildi",
                    'sale_price' => $stock->sale_price
                ]);
                
                \Log::info('Stok iade hareketi oluşturuldu', [
                    'movement_id' => $stockMovement->id,
                    'type' => 'giris',
                    'quantity' => $detail->quantity
                ]);
            }
            
            \Log::info('Stok iade işlemi başarıyla tamamlandı', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('Stok iade hatası', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function mapStatusToDb($status)
    {
        // Gelen durumu veritabanı ENUM değerleriyle eşleştir
        switch ($status) {
            case 'beklemede':
            case 'bekliyor':
            case 'sipariş alındı':
                return 'sipariş alındı';
                
            case 'hazırlanıyor':
            case 'hazirlaniyor':
                return 'hazırlanıyor';
                
            case 'hazır':
                return 'hazır';
                
            case 'teslim_edildi':
            case 'teslim edildi':
                return 'teslim edildi';
                
            case 'iptal':
            case 'iptal edildi':
                return 'iptal edildi';
                
            default:
                return 'sipariş alındı';
        }
    }
} 