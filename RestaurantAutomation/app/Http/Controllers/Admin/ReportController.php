<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $reportType = $request->input('report_type');

        // Tarih aralığı seçilmemişse varsayılan olarak son 30 günü al
        if (empty($startDate) && empty($endDate)) {
            $endDate = Carbon::today()->toDateString();
            $startDate = Carbon::today()->subDays(30)->toDateString();
        } else {
            // Eğer sadece başlangıç veya bitiş tarihi varsa, diğerini bugüne ayarla
            if (empty($startDate) && !empty($endDate)) {
                $startDate = Carbon::parse($endDate)->subDays(30)->toDateString(); // Bitişten 30 gün önce
            } elseif (!empty($startDate) && empty($endDate)) {
                $endDate = Carbon::today()->toDateString(); // Başlangıçtan bugüne
            }
        }

        // Alınan veya ayarlanan tarihleri logla (geçici)
        \Log::info('Rapor Filtreleri:', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'report_type' => $reportType,
        ]);

        // Determine if a specific report type is selected
        $isSpecificReportSelected = ($reportType !== null && $reportType !== '');
        $perPage = 15; // Default pagination for specific reports

        // Buraya tarih filtrelerine göre rapor verilerini çekme mantığı gelecek

        $topSellingProductsQuery = OrderDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->with('product') // İlişkili ürünü çek
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                // Siparişin teslim edildi veya ödendi durumda olması önemli olabilir
                // Şu an için sadece tarih filtresi uygulayalım
                // WHERE created_at >= $startDate AND created_at <= $endDate şeklinde filtreleme
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                 // Sadece 'teslim edildi' veya 'ödendi' durumundaki siparişleri dahil et (opsiyonel ama mantıklı)
                 $query->whereIn('status', ['teslim edildi', 'ödendi']);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity');

        // Apply limit or paginate based on report type
        if ($isSpecificReportSelected) {
            $topSellingProducts = $topSellingProductsQuery->paginate($perPage)->appends($request->except('page'));
        } else {
            $topSellingProducts = $topSellingProductsQuery->limit(5)->get();
        }

        \Log::info('En Çok Satan Ürünler:', $topSellingProducts->toArray());

        // Ürün Bazında Gelir Raporu
        $productRevenueQuery = OrderDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'), // Toplam satılan adet de gösterilebilir
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->with('product')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                $query->whereIn('status', ['teslim edildi', 'ödendi']);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_revenue');

        // Apply limit or paginate based on report type
        if ($isSpecificReportSelected) {
            $productRevenue = $productRevenueQuery->paginate($perPage)->appends($request->except('page'));
        } else {
            $productRevenue = $productRevenueQuery->limit(5)->get();
        }

        \Log::info('Ürün Bazında Gelir:', $productRevenue->toArray());

        // En Çok Kazandıran Müşteriler Raporu
        $topCustomersQuery = DB::table('customers')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('users', 'customers.user_id', '=', 'users.id') // users tablosuna join yapalım (nullable user_id için leftJoin)
            ->select(
                DB::raw('COALESCE(users.name, customers.name) as customer_name'), // Kayıtlı kullanıcı veya misafir adını al
                DB::raw('SUM(order_details.quantity * order_details.price) as total_spent')
            )
            ->where(function($query) { // İsmi boş olan misafirleri hariç tut
                $query->whereNotNull('customers.user_id') // Kayıtlı kullanıcılar
                      ->orWhere(function($query) {
                          $query->whereNull('customers.user_id') // Misafirler
                                ->whereNotNull('customers.name') // İsmi null olmayan misafirler
                                ->where('customers.name', '!=', ''); // İsmi boş olmayan misafirler
                      });
            })
            ->where(function($query) use ($startDate, $endDate) {
                 if ($startDate) {
                    $query->whereDate('orders.created_at', '>=', $startDate);
                 }
                 if ($endDate) {
                    $query->whereDate('orders.created_at', '<=', $endDate);
                 }
                 // Sadece tamamlanmış veya ödenmiş siparişleri dahil et
                 $query->whereIn('orders.status', ['teslim edildi', 'ödendi']);
            })
            // Kayıtlı kullanıcılar user_id'ye, misafirler (user_id null) isme göre gruplanacak
            // GROUP BY ifadesine users.name ve customers.name sütunlarını da ekleyerek hatayı düzeltiyoruz.
            ->groupBy(DB::raw('COALESCE(users.id, customers.name)'), 'users.name', 'customers.name')
            ->orderByDesc('total_spent');

        // Apply limit or paginate based on report type
        if ($isSpecificReportSelected) {
            $topCustomers = $topCustomersQuery->paginate($perPage)->appends($request->except('page'));
        } else {
            $topCustomers = $topCustomersQuery->limit(5)->get();
        }

        // Her müşteri grubu için en çok satın alınan ürünü bulmak için yeni bir sorgu
        // Note: This sub-query is for finding the most bought product for each customer and does not need pagination here.
        // It will be processed in the map function.
        $mostBoughtProductsForCustomers = DB::table('customers')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('users', 'customers.user_id', '=', 'users.id')
            ->select(
                DB::raw('COALESCE(users.id, customers.name) as customer_group_key'),
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
             ->where(function($query) { // İsmi boş olan misafirleri hariç tut
                $query->whereNotNull('customers.user_id') // Kayıtlı kullanıcılar
                      ->orWhere(function($query) {
                          $query->whereNull('customers.user_id') // Misafirler
                                ->whereNotNull('customers.name') // İsmi null olmayan misafirler
                                ->where('customers.name', '!=', ''); // İsmi boş olmayan misafirler
                      });
            })
            ->where(function($query) use ($startDate, $endDate) {
                 if ($startDate) {
                    $query->whereDate('orders.created_at', '>=', $startDate);
                 }
                 if ($endDate) {
                    $query->whereDate('orders.created_at', '<=', $endDate);
                 }
                 // Sadece tamamlanmış veya ödenmiş siparişleri dahil et
                 $query->whereIn('orders.status', ['teslim edildi', 'ödendi']);
            })
            ->groupBy('customer_group_key', 'products.name')
            ->orderByDesc('total_quantity')
            ->get(); // Keep get() here as it's used in the map function

        // Gruplanmış müşteri verilerine en çok satın alınan ürünü ekle
        // Note: This mapping is done after fetching paginated customers, so it processes only the current page's customers.
        $topCustomersWithMostBought = $topCustomers->map(function ($customer) use ($mostBoughtProductsForCustomers) {
            $customerGroupKey = $customer->customer_name; // Veya COALESCE ifadesinin aynısı
            
            // Bu müşteri grubuna ait en çok satın alınan ürünü bul
            $mostBoughtProduct = $mostBoughtProductsForCustomers
                ->where('customer_group_key', $customerGroupKey)
                ->first();

            $customer->most_bought_product = $mostBoughtProduct->product_name ?? 'Yok';
            return $customer;
        });

        \Log::info('En Çok Kazandıran Müşteriler (En Çok Satın Alınan Ürünle):', $topCustomersWithMostBought->toArray());

        // Garson Performans Raporu
        $waiterPerformanceQuery = DB::table('users')
            ->join('tables', 'users.id', '=', 'tables.waiter_id') // Garsonların sorumlu olduğu masaları birleştir
            ->join('orders', 'tables.id', '=', 'orders.table_id') // Masalara ait siparişleri birleştir
            ->join('order_details', 'orders.id', '=', 'order_details.order_id') // Sipariş detaylarını birleştir
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'), // Toplam sipariş sayısı
                DB::raw('SUM(order_details.quantity * order_details.price) as total_revenue') // Toplam gelir
            )
            ->where('users.role', 'waiter') // Sadece garsonları dahil et
            ->where(function($query) use ($startDate, $endDate) {
                 if ($startDate) {
                    $query->whereDate('orders.created_at', '>=', $startDate);
                 }
                 if ($endDate) {
                    $query->whereDate('orders.created_at', '<=', $endDate);
                 }
                 // Sadece tamamlanmış veya ödenmiş siparişleri dahil et
                 $query->whereIn('orders.status', ['teslim edildi', 'ödendi']);
            })
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue'); // Gelire göre sırala

        // Apply limit or paginate based on report type
        if ($isSpecificReportSelected) {
            $waiterPerformance = $waiterPerformanceQuery->paginate($perPage)->appends($request->except('page'));
        } else {
            $waiterPerformance = $waiterPerformanceQuery->limit(5)->get();
        }

        \Log::info('Garson Performansı:', $waiterPerformance->toArray());

        // En Az Satan Ürünler Raporu (Yeni Eklendi)
        $leastSellingProductsQuery = OrderDetail::select(
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * price) as total_revenue')
            )
            ->with('product') // İlişkili ürünü çek
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('created_at', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('created_at', '<=', $endDate);
                }
                 $query->whereIn('status', ['teslim edildi', 'ödendi']);
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity'); // En aza göre sırala

        // Apply limit or paginate based on report type
        if ($isSpecificReportSelected) {
            $leastSellingProducts = $leastSellingProductsQuery->paginate($perPage)->appends($request->except('page'));
        } else {
            $leastSellingProducts = $leastSellingProductsQuery->limit(5)->get();
        }

        \Log::info('En Az Satan Ürünler:', $leastSellingProducts->toArray());

        // Rapor verilerini view'a gönderelim
        return view('admin.reports', compact('topSellingProducts', 'productRevenue', 'topCustomers', 'leastSellingProducts', 'waiterPerformance'));
    }

    public function incomeExpense(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $months = [
            'Ocak' => 1, 'Şubat' => 2, 'Mart' => 3, 'Nisan' => 4, 'Mayıs' => 5, 'Haziran' => 6,
            'Temmuz' => 7, 'Ağustos' => 8, 'Eylül' => 9, 'Ekim' => 10, 'Kasım' => 11, 'Aralık' => 12
        ];
        $monthNumber = $months[$month] ?? 1;

        // GELİR: Satış (stok çıkışları)
        $income = DB::table('stock_movements')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->where('type', 'cikis')
            ->select(DB::raw('SUM(sale_price * quantity) as total'))
            ->value('total') ?? 0;

        // GİDER: Alış (stok girişleri)
        $expense = DB::table('stock_movements')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->where('type', 'giris')
            ->select(DB::raw('SUM(purchase_price * quantity) as total'))
            ->value('total') ?? 0;

        $net = $income - $expense;

        // Detaylı tablo için veriler
        $details = DB::table('stock_movements')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNumber)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'income' => number_format($income, 2, ',', '.'),
            'expense' => number_format($expense, 2, ',', '.'),
            'net' => number_format($net, 2, ',', '.'),
            'details' => $details,
        ]);
    }
} 