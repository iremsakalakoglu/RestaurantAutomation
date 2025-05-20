<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function orderPage(Request $request)
    {
        $tableId = $request->query('table');
        $table = null;
        
        if ($tableId) {
            $table = Table::findOrFail($tableId);
        }

        $ordersQuery = Order::whereHas('customer', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['orderDetails.product', 'table', 'payment']);

        // Fiyata göre sıralama için toplam fiyatı hesapla
        $ordersQuery = $ordersQuery->withSum('orderDetails as total_price', \DB::raw('price * quantity'));

        // Sıralama parametresi
        $orderBy = 'created_at';
        $orderDir = 'desc';
        if ($request->filled('price_order')) {
            $orderBy = 'total_price';
            $orderDir = $request->price_order;
        }

        $orders = $ordersQuery->orderBy($orderBy, $orderDir)->paginate(5);

        return view('orderhistory', compact('orders'));
    }
    
    public function storeOrder(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            \DB::beginTransaction();
            
            $table = Table::findOrFail($request->table_id);
            
            // Kullanıcının customer kaydını al veya oluştur
            $user = Auth::user();
            $customer = $user->customer;
            if (!$customer) {
                $customer = \App\Models\Customer::create(['user_id' => $user->id]);
            }

            // Siparişi oluştur
            $order = Order::create([
                'customer_id' => $customer->id,
                'table_id' => $table->id,
                'status' => 'sipariş alındı',
                'payment_status' => 'bekliyor'
            ]);

            // Sipariş detaylarını ekle
            foreach ($request->products as $product) {
                $productModel = Product::findOrFail($product['id']);
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $productModel->price,
                    'is_delivered' => false,
                    'is_paid' => false,
                    'payment_status' => 'bekliyor'
                ]);
            }

            // Masanın durumunu güncelle
            $table->status = 'dolu';
            $table->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla alındı'
            ]);

        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Sipariş oluşturma hatası: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sipariş oluşturulurken bir hata oluştu'
            ], 500);
        }
    }

    public function repeatOrder($id)
    {
        $oldOrder = Order::with('orderDetails.product')->findOrFail($id);
        $user = Auth::user();
        $customer = $user->customer;
        if (!$customer) {
            $customer = \App\Models\Customer::create(['user_id' => $user->id]);
        }
        // Yeni siparişi oluştur
        $newOrder = Order::create([
            'customer_id' => $customer->id,
            'table_id' => $oldOrder->table_id,
            'status' => 'sipariş alındı',
        ]);
        // Sipariş detaylarını ekle
        foreach ($oldOrder->orderDetails as $detail) {
            OrderDetail::create([
                'order_id' => $newOrder->id,
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'is_delivered' => false
            ]);
        }
        return redirect()->route('order.history')->with('success', 'Siparişiniz başarıyla tekrar verildi!');
    }
}
