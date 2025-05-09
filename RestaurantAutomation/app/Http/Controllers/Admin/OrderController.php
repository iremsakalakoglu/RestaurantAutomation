<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'table', 'orderDetails.product'])
            ->orderBy('created_at', 'desc');
        
        // Sipariş durumu filtresi
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Tarih aralığı filtresi
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Ödeme durumu filtresi
        if ($request->has('payment') && $request->payment) {
            if ($request->payment === 'tamamlandı') {
                $query->where('status', 'tamamlandı');
            } else {
                $query->where('status', '!=', 'tamamlandı');
            }
        }
        
        // Arama filtresi
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                // Sipariş ID'sine göre ara
                $q->where('id', 'like', "%{$search}%")
                // Veya ilişkili masa numarasına göre ara
                ->orWhereHas('table', function($subq) use ($search) {
                    $subq->where('table_number', 'like', "%{$search}%");
                });
            });
        }
        
        $orders = $query->paginate(10)->withQueryString();
        
        return view('Dashboard.orders', compact('orders'));
    }
    
    public function show($id)
    {
        $order = Order::with(['customer', 'table', 'orderDetails.product'])
            ->findOrFail($id);
            
        return response()->json($order);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:bekliyor,hazırlanıyor,hazır,teslim,ödendi,iptal'
        ]);
        
        \Log::info('Admin: Sipariş durumu güncelleme isteği alındı', [
            'order_id' => $id,
            'status' => $request->status
        ]);
        
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();
        
        // Bildirim ekle
        if ($order->customer_id) {
            $message = null;
            switch ($request->status) {
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
                    \Log::info('Notification başarıyla oluşturuldu', ['notification' => $notification]);
                } catch (\Exception $e) {
                    \Log::error('Notification oluşturulamadı', ['error' => $e->getMessage()]);
                }
            }
        }
        
        \Log::info('Admin: Sipariş durumu güncellendi', [
            'order_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $request->status
        ]);
        
        // Eğer sipariş ödendi olarak işaretlendiyse, masayı boş olarak güncelle
        if ($request->status === 'ödendi' && $order->table_id) {
            $table = Table::find($order->table_id);
            if ($table) {
                \Log::info('Admin: Masa durumu güncelleniyor', [
                    'table_id' => $table->id,
                    'old_status' => $table->status
                ]);
                
                $table->status = 'boş';
                $table->save();
                
                \Log::info('Admin: Masa durumu güncellendi', [
                    'table_id' => $table->id,
                    'new_status' => $table->status
                ]);
            } else {
                \Log::warning('Admin: Sipariş için masa bulunamadı', [
                    'order_id' => $id,
                    'table_id' => $order->table_id
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Sipariş durumu başarıyla güncellendi'
        ]);
    }
}
