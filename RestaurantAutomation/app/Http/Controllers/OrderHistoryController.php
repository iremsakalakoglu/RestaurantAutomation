<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderHistoryController extends Controller
{
    public function index()
    {
        $orders = Order::whereHas('customer', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['orderDetails.product', 'table', 'payment'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('orderhistory', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::whereHas('customer', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['orderDetails.product', 'table', 'payment'])
        ->findOrFail($id);

        return view('orderhistory-detail', compact('order'));
    }

    public function favorites()
    {
        $userId = Auth::id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();
        $favoriteProducts = collect();
        if ($customer) {
            // Son 10 siparişi al
            $last10Orders = $customer->orders()->orderBy('created_at', 'desc')->take(10)->pluck('id');
            // Bu siparişlerdeki ürünleri grupla ve say
            $productCounts = \App\Models\OrderDetail::whereIn('order_id', $last10Orders)
                ->select('product_id', \DB::raw('COUNT(*) as count'))
                ->groupBy('product_id')
                ->having('count', '>', 4)
                ->pluck('count', 'product_id');
            if ($productCounts->count() > 0) {
                $favoriteProducts = \App\Models\Product::whereIn('id', $productCounts->keys())->get();
                // Her ürünün kaç kez sipariş edildiğini ekle
                $favoriteProducts->map(function($product) use ($productCounts) {
                    $product->order_count = $productCounts[$product->id];
                    return $product;
                });
            }
        }
        return view('favorites', compact('favoriteProducts'));
    }

    public function notifications()
    {
        $userId = Auth::id();
        $customer = \App\Models\Customer::where('user_id', $userId)->first();
        $notifications = collect();
        if ($customer) {
            $notifications = \App\Models\Notification::where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->take(30)
                ->get();
        }
        return view('notification', compact('notifications'));
    }
} 