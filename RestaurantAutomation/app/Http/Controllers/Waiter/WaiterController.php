<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class WaiterController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (Auth::user()->role !== 'waiter') {
            return redirect()->route('menu')->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        // Get tables assigned to the current waiter
        $assignedTables = Table::with(['currentOrder.orderDetails.product'])
            ->where('waiter_id', Auth::id())
            ->orderBy('table_number')
            ->get();
        
        // Get all orders for assigned tables
        $activeOrders = Order::whereIn('table_id', $assignedTables->pluck('id'))
            ->whereIn('status', ['sipariş alındı', 'hazırlanıyor', 'hazır'])
            ->with(['table', 'orderDetails.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get delivered orders for assigned tables (only if all items are delivered)
        $deliveredOrders = Order::whereIn('table_id', $assignedTables->pluck('id'))
            ->where('status', 'teslim edildi')
            ->whereDoesntHave('orderDetails', function($query) {
                $query->where('is_delivered', false);
            })
            ->with(['table', 'orderDetails.product'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('waiter.waiter', [
            'assignedTables' => $assignedTables,
            'activeOrders' => $activeOrders,
            'deliveredOrders' => $deliveredOrders
        ]);
    }

    public function orders()
    {
        $orders = Order::whereHas('table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->orderBy('created_at', 'desc')->paginate(10);
        
        return view('waiter.orders', [
            'orders' => $orders
        ]);
    }

    public function showOrder($id)
    {
        $order = Order::whereHas('table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->with(['orderDetails.product'])->findOrFail($id);
        
        return view('waiter.order-detail', [
            'order' => $order
        ]);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::whereHas('table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:teslim edildi,iptal edildi'
        ]);

        $order->status = $request->status;
        
        if ($request->status === 'teslim edildi') {
            $order->orderDetails()->update(['is_delivered' => true]);
            
            // Masayı boşalt
            if ($order->table) {
                $order->table->status = 'boş';
                $order->table->save();
            }
        }
        
        $order->save();

        return redirect()->back()->with('success', 'Sipariş durumu güncellendi');
    }

    public function updateOrderDetailDelivery(Request $request, $id)
    {
        $orderDetail = OrderDetail::whereHas('order.table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->findOrFail($id);

        $orderDetail->is_delivered = true;
        $orderDetail->save();

        // Tüm ürünler teslim edildiyse siparişi tamamla
        $allDelivered = $orderDetail->order->orderDetails()
            ->where('is_delivered', false)
            ->doesntExist();

        if ($allDelivered) {
            $orderDetail->order->status = 'teslim edildi';
            $orderDetail->order->save();
        }

        return redirect()->back()->with('success', 'Ürün teslim edildi olarak işaretlendi');
    }

    public function closeOrder(Request $request, $id)
    {
        $order = Order::whereHas('table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->findOrFail($id);

        // Siparişin durumunu 'ödeme bekliyor' olarak güncelle
        $order->status = 'ödeme bekliyor';
        $order->save();

        // Masayı boş olarak işaretle
        if ($order->table) {
            $order->table->status = 'boş';
            $order->table->save();
        }

        return redirect()->back()->with('success', 'Adisyon kapatıldı ve kasiyer paneline gönderildi');
    }

    public function clearTable(Request $request, $id)
    {
        $table = Table::where('waiter_id', Auth::id())->findOrFail($id);
        
        // Masayı boş olarak işaretle
        $table->status = 'boş';
        $table->save();

        return redirect()->back()->with('success', 'Masa boşaltıldı');
    }

    public function cancelOrderDetail(Request $request, $id)
    {
        $orderDetail = OrderDetail::whereHas('order.table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->findOrFail($id);

        // Sipariş detayını iptal et
        $orderDetail->delete();

        // Eğer siparişin tüm detayları silindiyse siparişi de iptal et
        if ($orderDetail->order->orderDetails()->count() === 0) {
            $orderDetail->order->status = 'iptal edildi';
            $orderDetail->order->save();

            // Masayı boşalt
            if ($orderDetail->order->table) {
                $orderDetail->order->table->status = 'boş';
                $orderDetail->order->table->save();
            }
        }

        return redirect()->back()->with('success', 'Ürün siparişi iptal edildi');
    }

    public function addProductToOrder(Request $request, $orderId)
    {
        $order = Order::whereHas('table', function($query) {
            $query->where('waiter_id', Auth::id());
        })->findOrFail($orderId);

        // Validate request
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Create new order detail
        $product = Product::findOrFail($request->product_id);
        $orderDetail = new OrderDetail([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => $product->current_price,
            'is_ready' => false,
            'is_delivered' => false
        ]);

        $order->orderDetails()->save($orderDetail);

        // Update order status if needed
        if ($order->status === 'teslim edildi') {
            $order->status = 'hazırlanıyor';
            $order->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla eklendi'
        ]);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'customer_name' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // 1. Customer kaydı oluştur (misafir için)
        $customer = \App\Models\Customer::create([
            'user_id' => null,
            'name' => $request->customer_name
        ]);

        // 2. Masa durumunu güncelle
        $table = \App\Models\Table::findOrFail($request->table_id);
        $table->status = 'dolu';
        $table->save();

        // 3. Siparişi oluştur
        $order = \App\Models\Order::create([
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'status' => 'sipariş alındı',
        ]);

        // 4. Sipariş detaylarını ekle
        foreach ($request->products as $item) {
            $product = \App\Models\Product::findOrFail($item['id']);
            $order->orderDetails()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->current_price,
                'is_ready' => false,
                'is_delivered' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sipariş başarıyla oluşturuldu.'
        ]);
    }
}