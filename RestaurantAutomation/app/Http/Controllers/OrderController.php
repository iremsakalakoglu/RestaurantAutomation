<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function orderPage(Request $request)
    {
        $tableId = $request->query('table');
        $table = null;
        
        if ($tableId) {
            $table = Table::findOrFail($tableId);
        }

        $orders = Order::whereHas('customer', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['orderDetails.product', 'table', 'payment'])
        ->orderBy('created_at', 'desc')
        ->paginate(5);

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
        
        $table = Table::findOrFail($request->table_id);
        
        // Update table status to occupied
        $table->status = 'dolu';
        $table->save();
        
        // Create order logic will go here
        // This would typically include creating an order record and order details
        
        return response()->json([
            'success' => true,
            'message' => 'Sipariş başarıyla alındı'
        ]);
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
