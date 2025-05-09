<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private function checkAdminAccess()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('menu');
        }
        return null;
    }

    private function getLastSevenDaysData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // O günün cirosu
            $dailyRevenue = Payment::whereDate('created_at', $date)
                ->where('status', 'tamamlandi')
                ->sum('amount');
            
            // O günün sipariş sayısı
            $orderCount = Order::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('d.m'),
                'revenue' => $dailyRevenue,
                'orders' => $orderCount
            ];
        }
        return $data;
    }

    private function getCustomerGrowthData()
    {
        $data = [];
        $previousMonthCount = 0;

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::today()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // O ay içinde kaydolan müşteri sayısı
            $newCustomers = User::where('role', 'customer')
                ->whereNotNull('name')
                ->whereNotNull('email')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            // Büyüme oranı hesaplama
            $growthRate = 0;
            if ($previousMonthCount == 0) {
                // Eğer önceki ay 0 müşteri varsa ve bu ay yeni müşteri varsa
                if ($newCustomers > 0) {
                    $growthRate = 100; // İlk müşteriler geldi
                }
            } else {
                // Normal büyüme oranı hesaplama
                $growthRate = (($newCustomers - $previousMonthCount) / $previousMonthCount) * 100;
            }

            $data[] = [
                'month' => $date->format('M Y'),
                'customers' => $newCustomers,
                'growth_rate' => round($growthRate, 1),
                'previous_month' => $previousMonthCount // Debug için ekledim
            ];

            $previousMonthCount = $newCustomers;
        }

        return $data;
    }

    private function getTopSellingProducts()
    {
        return DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'quantity' => (int)$item->total_quantity
                ];
            });
    }

    public function index()
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            // Günlük satış toplamını hesapla
            $dailySales = Payment::whereDate('created_at', Carbon::today())
                ->where('status', 'tamamlandi')
                ->sum('amount');

            // Toplam sipariş sayısını hesapla
            $totalOrders = Order::count();

            // Toplam müşteri sayısı (sadece gerçek kullanıcılar)
            $totalCustomers = User::where('role', 'customer')
                ->whereNotNull('name')
                ->whereNotNull('email')
                ->count();

            // Kritik stok seviyesindeki ürünler (5 ve altındaki stoklar)
            $stockAlerts = Stock::where('quantity', '<=', 5)->count();

            // Son 7 günün verilerini al
            $lastSevenDaysData = $this->getLastSevenDaysData();

            // Müşteri artış verilerini al
            $customerGrowthData = $this->getCustomerGrowthData();

            // En çok satan ürünleri al
            $topSellingProducts = $this->getTopSellingProducts();

            // Son siparişleri al
            $recentOrders = Order::with(['customer', 'customer.user', 'orderDetails'])
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            // Her sipariş için toplam tutarı hesapla ve müşteri adını ayarla
            foreach ($recentOrders as $order) {
                // Toplam tutar hesaplama
                $totalAmount = 0;
                foreach ($order->orderDetails as $detail) {
                    $totalAmount += $detail->quantity * $detail->price;
                }
                $order->total_amount = $totalAmount;
                
                // Müşteri adı kontrolü
                if ($order->customer && $order->customer->user) {
                    $customerName = $order->customer->user->name;
                    $customerLastName = $order->customer->user->lastName ?? '';
                    $order->customer_name = $customerName . ($customerLastName ? ' ' . $customerLastName : '');
                } else {
                    $order->customer_name = "Misafir";
                }
            }

            return view('Dashboard.dashboard', compact(
                'dailySales',
                'totalOrders',
                'totalCustomers',
                'stockAlerts',
                'recentOrders',
                'lastSevenDaysData',
                'customerGrowthData',
                'topSellingProducts'
            ));

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            return view('Dashboard.dashboard', [
                'dailySales' => 0,
                'totalOrders' => 0,
                'totalCustomers' => 0,
                'stockAlerts' => 0,
                'recentOrders' => collect([]),
                'lastSevenDaysData' => [],
                'customerGrowthData' => [],
                'topSellingProducts' => []
            ])->with('error', 'Veriler yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }
}
