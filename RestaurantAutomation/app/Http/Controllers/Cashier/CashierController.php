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

        // Get orders that are ready for payment (completed but not paid)
        $pendingOrders = Order::where('status', 'tamamlandi')
            ->whereDoesntHave('payment', function($query) {
                $query->where('status', 'tamamlandi');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get today's completed payments
        $todayPayments = Payment::where('status', 'tamamlandi')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

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
        $payment->status = 'tamamlandi';
        $payment->processed_by = Auth::id();
        $payment->save();

        // Update order status
        $order->status = 'tamamlandi';
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
        // Masaya ait ödeme bekleyen (teslim edilen ve tüm ürünleri teslim edilmiş) siparişi bul
        $order = Order::with(['orderDetails.product', 'table', 'payment'])
            ->where('table_id', $tableId)
            ->where('status', 'teslim edildi')
            ->whereDoesntHave('orderDetails', function($query) {
                $query->where('is_delivered', false);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$order) {
            return response('<div class="p-6 text-center text-gray-500">Bu masada ödeme bekleyen adisyon yok.</div>', 404);
        }

        return view('cashier.partials.adisyon', compact('order'));
    }

    public function payOrderDetail($detailId)
    {
        try {
            $orderDetail = OrderDetail::findOrFail($detailId);
            
            // Ürünün ödenip ödenmediğini kontrol et
            if ($orderDetail->is_paid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün zaten ödenmiş.'
                ], 400);
            }

            // Ürünü ödenmiş olarak işaretle
            $orderDetail->is_paid = true;
            $orderDetail->save();

            // Tüm ürünler ödendiyse siparişi tamamla
            $allPaid = $orderDetail->order->orderDetails()
                ->where('is_paid', false)
                ->doesntExist();

            if ($allPaid) {
                $orderDetail->order->status = 'tamamlandi';
                $orderDetail->order->save();

                // Masayı boşalt
                if ($orderDetail->order->table) {
                    $orderDetail->order->table->status = 'boş';
                    $orderDetail->order->table->save();
                }
            }

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
            return response()->json([
                'success' => false,
                'message' => 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}