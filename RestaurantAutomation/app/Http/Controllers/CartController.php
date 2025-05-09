<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Table;

class CartController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        try {
            // Masa kontrolü
            $tableId = $request->query('table');
            if (empty($tableId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lütfen önce bir masa seçimi yapınız.',
                    'type' => 'warning'
                ], 400);
            }

            DB::beginTransaction();
            
            $product = Product::with(['stock', 'category'])->findOrFail($id);
            
            // Stok kontrolü
            if (!$product->stock || $product->stock->quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün şu anda stokta bulunmamaktadır.',
                    'type' => 'error'
                ], 400);
            }

            // Aktif siparişlerdeki rezerve edilmiş ürünleri kontrol et
            $reservedQuantity = OrderDetail::whereHas('order', function($query) {
                $query->whereIn('status', ['bekliyor', 'hazirlaniyor']);
            })->where('product_id', $id)->sum('quantity');

            // Mevcut sepetteki miktar
            $cart = session()->get('cart', []);
            $currentReservation = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
            $requestedQuantity = $currentReservation + 1;
            
            // Gerçek stok miktarı = Stok - Rezerve edilmiş miktar
            $availableStock = $product->stock->quantity - $reservedQuantity;
            
            // Yeterli stok var mı kontrol et
            if ($availableStock <= 0 || $availableStock < $requestedQuantity) {
                $message = $availableStock <= 0 
                    ? 'Bu ürün şu anda başka siparişler için rezerve edilmiş durumda.'
                    : 'Üzgünüz, bu üründen yalnızca ' . $availableStock . ' adet kaldı.';
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'type' => 'warning'
                ], 400);
            }
            
            // Stok miktarını rezerve et
            if (!isset($cart[$id])) {
                session()->push('reserved_stock', [
                    'product_id' => $id,
                    'quantity' => 1,
                    'timestamp' => now()
                ]);
            }

            if (isset($cart[$id])) {
                $cart[$id]['quantity']++;
            } else {
                $cart[$id] = [
                    "name" => $product->name,
                    "quantity" => 1,
                    "price" => $product->stock->sale_price,
                    "image" => $product->image
                ];
            }
            
            // Masa bilgisini session'a kaydet
            session()->put('table_id', $tableId);
            session()->put('cart', $cart);
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi.',
                'type' => 'success',
                'cart_count' => array_sum(array_column($cart, 'quantity'))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sepete ekleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ürün sepete eklenirken bir hata oluştu.',
                'type' => 'error'
            ], 500);
        }
    }

    public function removeFromCart(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $cart = session()->get('cart', []);
            
            // Sepetten ürün kaldırılırken rezervasyonu da kaldır
            if(isset($cart[$id])) {
                $quantity = $cart[$id]['quantity'];
                
                // Rezervasyonu kaldır
                $reservations = session()->get('reserved_stock', []);
                foreach ($reservations as $key => $reservation) {
                    if ($reservation['product_id'] == $id) {
                        unset($reservations[$key]);
                        break;
                    }
                }
                session()->put('reserved_stock', array_values($reservations));
                
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
            
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ürün sepetten kaldırıldı',
                    'cart_count' => array_sum(array_column($cart, 'quantity'))
                ]);
            }

            // Masa bilgisini koru
            $tableId = $request->query('table');
            if ($tableId) {
                return redirect()->route('cart', ['table' => $tableId])->with('success', 'Ürün sepetten kaldırıldı');
            }
            
            return redirect()->route('cart')->with('success', 'Ürün sepetten kaldırıldı');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün sepetten kaldırılırken bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('cart')->with('error', 'Ürün sepetten kaldırılırken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function checkout(Request $request)
    {
        // Sepet boş mu kontrol et
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sepetiniz boş!'
                ], 400);
            }
            return redirect()->back()->with('error', 'Sepetiniz boş!');
        }

        try {
            \Log::info('Sipariş oluşturma işlemi başladı', [
                'session_id' => session()->getId(),
                'cart_items' => count($cart),
                'table_id' => $request->input('table_id', null),
                'is_ajax' => $request->expectsJson()
            ]);
            
            // Masa durumunu kontrol et
            $tableId = $request->input('table_id');
            if ($tableId) {
                $table = Table::findOrFail($tableId);
                
                if ($table->status === 'dolu') {
                    // Masa dolu ise, aktif siparişi kontrol et
                    $activeOrder = Order::where('table_id', $tableId)
                        ->whereIn('status', ['bekliyor', 'hazirlaniyor', 'hazir'])
                        ->first();
                    
                    if ($activeOrder) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Bu masa şu anda kullanımda. Lütfen başka bir masa seçin.'
                            ], 400);
                        }
                        return redirect()->back()->with('error', 'Bu masa şu anda kullanımda. Lütfen başka bir masa seçin.');
                    }
                }
            }
            
            // Transaction başlatılıyor
            DB::beginTransaction();

            // 1. Müşteri oluşturma - basitleştirilmiş
            $customer = null;
            if (Auth::check()) {
                $customer = Customer::firstOrCreate(['user_id' => Auth::id()]);
            } else {
                $customer = Customer::create(['user_id' => null]);
            }
            
            // 2. Sipariş oluşturma
            try {
                // Tablo yapısını kontrol et
                $orderColumns = [];
                try {
                    $columns = DB::select("SHOW COLUMNS FROM orders");
                    foreach ($columns as $column) {
                        $orderColumns[] = $column->Field;
                    }
                    \Log::info('Orders tablosu yapısı', ['columns' => $orderColumns]);
                } catch (\Exception $e) {
                    \Log::warning('Orders tablo yapısı alınamadı, varsayılan yapı kullanılacak', [
                        'error' => $e->getMessage()
                    ]);
                    $orderColumns = ['id', 'customer_id', 'table_id', 'status', 'created_at', 'updated_at'];
                }
                
                // Sipariş verisi oluştur
                $orderData = [];
                
                // Temel alanlar
                if (in_array('customer_id', $orderColumns)) {
                    $orderData['customer_id'] = $customer->id;
                }
                
                // Status alanı (varsa enum değerlerini kontrol et)
                if (in_array('status', $orderColumns)) {
                    // Enum değerlerini kontrol et
                    try {
                        $statusInfo = DB::select("SHOW COLUMNS FROM orders WHERE Field = 'status'");
                        if (!empty($statusInfo) && isset($statusInfo[0]->Type) && strpos($statusInfo[0]->Type, 'enum') !== false) {
                            // Enum değerlerini çıkar
                            preg_match('/enum\((.*?)\)/', $statusInfo[0]->Type, $matches);
                            $enumValues = [];
                            if (isset($matches[1])) {
                                // Tırnak işaretleri ile ayırılmış değerleri al
                                preg_match_all("/'([^']+)'/", $matches[1], $enumMatches);
                                if (isset($enumMatches[1])) {
                                    $enumValues = $enumMatches[1];
                                }
                            }
                            
                            \Log::info('Status için enum değerleri:', ['values' => $enumValues]);
                            
                            // İzin verilen değerleri kontrol et
                            if (in_array('bekliyor', $enumValues)) {
                                $orderData['status'] = 'bekliyor';
                            } else if (in_array('beklemede', $enumValues)) {
                                $orderData['status'] = 'beklemede';
                            } else if (!empty($enumValues)) {
                                // Hiç değilse ilk enum değerini kullan
                                $orderData['status'] = $enumValues[0];
                            } else {
                                // Hiç enum değeri yoksa güvenli bir değer kullan
                                $orderData['status'] = 'sipariş alındı';
                            }
                        } else {
                            // Enum değilse, genel bir değer kullan
                            $orderData['status'] = 'sipariş alındı';
                        }
                    } catch(\Exception $e) {
                        \Log::warning('Status enum değerleri alınamadı', ['error' => $e->getMessage()]);
                        $orderData['status'] = 'sipariş alındı';
                    }
                }
                
                // Table ID
                if (in_array('table_id', $orderColumns) && $request->filled('table_id')) {
                    $orderData['table_id'] = $request->input('table_id');
                }
                
                // Timestamp alanları
                if (in_array('created_at', $orderColumns)) {
                    $orderData['created_at'] = now();
                }
                
                if (in_array('updated_at', $orderColumns)) {
                    $orderData['updated_at'] = now();
                }
                
                // Siparişi oluştur
                $orderId = DB::table('orders')->insertGetId($orderData);
                
                if (!$orderId) {
                    throw new \Exception('Sipariş kaydedilemedi');
                }
                
                \Log::info('Sipariş SQL sorgusu ile kaydedildi', ['order_id' => $orderId, 'data' => $orderData]);
            } catch (\Exception $e) {
                \Log::error('Sipariş oluşturma hatası', [
                    'error' => $e->getMessage(),
                    'customer_id' => $customer->id
                ]);
                throw $e;
            }
            
            // 3. Sipariş detaylarını ekleme
            $orderTotal = 0;
            $orderDetailErrors = [];
            
            // Tablo kolonlarını dinamik tespit et
            try {
                $columns = DB::select("SHOW COLUMNS FROM order_details");
                $tableColumns = [];
                
                foreach ($columns as $column) {
                    $tableColumns[] = $column->Field;
                }
                
                \Log::info('Order details tablo yapısı', ['columns' => $tableColumns]);
            } catch (\Exception $e) {
                \Log::warning('Tablo yapısı alınamadı, varsayılan yapı kullanılacak', [
                    'error' => $e->getMessage()
                ]);
                $tableColumns = ['order_id', 'product_id', 'quantity', 'price', 'created_at', 'updated_at'];
            }
            
            foreach ($cart as $productId => $item) {
                try {
                    // Tablo yapısına uygun veri oluştur
                    $orderDetailData = [];
                    
                    // order_id sütun adını tespit et
                    $orderIdColumnName = 'order_id';
                    if (in_array('c_odr', $tableColumns)) {
                        $orderIdColumnName = 'c_odr'; 
                    } else if (in_array('order_id', $tableColumns)) {
                        $orderIdColumnName = 'order_id';
                    }
                    
                    // Temel veri yapısı
                    $orderDetailData[$orderIdColumnName] = $orderId;
                    
                    // Diğer alanlar
                    if (in_array('product_id', $tableColumns)) {
                        $orderDetailData['product_id'] = $productId;
                    }
                    
                    if (in_array('quantity', $tableColumns)) {
                        $orderDetailData['quantity'] = $item['quantity'];
                    }
                    
                    if (in_array('price', $tableColumns)) {
                        $orderDetailData['price'] = $item['price'];
                    }
                    
                    // Timestamp alanları
                    if (in_array('created_at', $tableColumns)) {
                        $orderDetailData['created_at'] = now();
                    }
                    
                    if (in_array('updated_at', $tableColumns)) {
                        $orderDetailData['updated_at'] = now();
                    }
                    
                    \Log::info('Sipariş detayı veri yapısı', ['data' => $orderDetailData]);
                    
                    // Siparişi ekle
                    DB::table('order_details')->insert($orderDetailData);
                    \Log::info('Sipariş detayı eklendi', ['order_id' => $orderId, 'product_id' => $productId]);
                    
                    $orderTotal += ($item['price'] * $item['quantity']);
                    
                } catch (\Exception $detailError) {
                    $orderDetailErrors[] = "Ürün detayı eklenirken hata: " . $detailError->getMessage();
                    \Log::error('Sipariş detayı eklenirken hata', [
                    'product_id' => $productId,
                        'error' => $detailError->getMessage(),
                        'trace' => $detailError->getTraceAsString()
                    ]);
                }
            }
            
            // Eğer hiçbir ürün detayı eklenemezse hata fırlatma
            if (count($orderDetailErrors) == count($cart)) {
                throw new \Exception('Hiçbir ürün detayı eklenemedi: ' . implode(', ', $orderDetailErrors));
            }
            
            // 4. Eğer masa seçilmişse masayı dolu işaretleme
            if ($request->filled('table_id')) {
                try {
                    // Tablo yapısını kontrol et
                    $tableColumns = [];
                    try {
                        $columns = DB::select("SHOW COLUMNS FROM tables");
                        foreach ($columns as $column) {
                            $tableColumns[] = $column->Field;
                        }
                        \Log::info('Table tablosu yapısı', ['columns' => $tableColumns]);
                    } catch (\Exception $e) {
                        \Log::warning('Tablo yapısı alınamadı, varsayılan yapı kullanılacak', [
                            'error' => $e->getMessage()
                        ]);
                        $tableColumns = ['id', 'table_number', 'status', 'created_at', 'updated_at'];
                    }
                    
                    // Güncelleme için veri oluştur
                    $updateData = [];
                    
                    // Status alanı varsa güncelle
                    if (in_array('status', $tableColumns)) {
                        $updateData['status'] = 'dolu';
                    }
                    
                    // Timestamp alanı varsa güncelle
                    if (in_array('updated_at', $tableColumns)) {
                        $updateData['updated_at'] = now();
                    }
                    
                    // Veri yoksa işlem yapma
                    if (empty($updateData)) {
                        \Log::warning('Masa güncellenemiyor: Uygun alanlar bulunamadı');
                    } else {
                        // Doğrudan SQL ile güncelleme
                        $updated = DB::table('tables')
                            ->where('id', $request->input('table_id'))
                            ->update($updateData);
                        
                        if ($updated) {
                            \Log::info('Masa durumu SQL ile güncellendi', [
                                'table_id' => $request->input('table_id'),
                                'data' => $updateData
                            ]);
                        } else {
                            \Log::warning('Masa güncellenemedi', [
                                'table_id' => $request->input('table_id')
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Masa güncellenirken hata', [
                        'table_id' => $request->input('table_id'),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Bu hatada işlemi durdurmuyoruz, devam ediyoruz
                }
            }
            
            // 5. Sepeti temizleme
            session()->forget('cart');
            session()->forget('reserved_stock');
            
            // İşlemleri onaylama
            DB::commit();
            \Log::info('Sipariş başarıyla tamamlandı', ['order_id' => $orderId]);
            
            // JSON isteği ise JSON cevap döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Siparişiniz başarıyla oluşturuldu! Sipariş no: #' . $orderId,
                    'order_id' => $orderId,
                    'redirect_url' => route('menu', ['table' => $request->input('table_id')])
                ]);
            }
            
            // Normal istek ise yönlendirme yap
            return redirect()->route('menu', ['table' => $request->input('table_id')])
                ->with('success', 'Siparişiniz başarıyla oluşturuldu! Sipariş no: #' . $orderId);
        } 
        catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sipariş oluşturma hatası', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            // Detaylı hata analizi
            $errorMessage = 'Sipariş oluşturulurken bir hata oluştu. ';
            
            // Bilinmeyen sütun hatası
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                // Sütun ismini çıkar
                preg_match('/Unknown column \'(.*?)\'/', $e->getMessage(), $matches);
                $columnName = isset($matches[1]) ? $matches[1] : 'bilinmeyen sütun';
                
                $errorMessage .= "Veritabanında '$columnName' sütunu bulunamadı. Lütfen yöneticinize bildirin.";
                
                \Log::error('Bilinmeyen sütun hatası tespit edildi', [
                    'column_name' => $columnName,
                    'message' => $e->getMessage()
                ]);
            }
            // Sütun eksik hatası 
            else if (strpos($e->getMessage(), 'doesn\'t have a default value') !== false || 
                     strpos($e->getMessage(), 'cannot be null') !== false) {
                // Sütun ismini çıkar
                preg_match('/\'(.*?)\'/', $e->getMessage(), $matches);
                $columnName = isset($matches[1]) ? $matches[1] : 'bilinmeyen alan';
                
                $errorMessage .= "Veritabanı '$columnName' sütunu için değer gerekli. Lütfen yöneticinize bildirin.";
                
                \Log::error('Boş değer hatası tespit edildi', [
                    'column_name' => $columnName,
                    'message' => $e->getMessage()
                ]);
            }
            // SQL hatalarını yönet
            else if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                // Enum değer hatası
                if (strpos($e->getMessage(), 'truncated') !== false) {
                    // Hangi sütun için truncated hatası oluştu
                    preg_match('/column \'(.*?)\'/', $e->getMessage(), $matches);
                    $columnName = isset($matches[1]) ? $matches[1] : 'bilinmeyen alan';
                    
                    $errorMessage .= "'$columnName' değeri çok uzun. Lütfen yöneticinize bildirin.";
                }
                // Tablo bulunamadı hatası
                else if (strpos($e->getMessage(), 'table') !== false && strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                    // Hangi tablo bulunamadı
                    preg_match('/Table \'(.*?)\'/', $e->getMessage(), $matches);
                    $tableName = isset($matches[1]) ? $matches[1] : 'bilinmeyen tablo';
                    
                    $errorMessage .= "'$tableName' tablosu bulunamadı. Lütfen yöneticinize bildirin.";
                }
                // Foreign key hatası
                else if (strpos($e->getMessage(), 'foreign key') !== false) {
                    $errorMessage .= 'İlişkili bir kayıt bulunamadı. Lütfen yöneticinize bildirin.';
                }
                // Duplicate key hatası
                else if (strpos($e->getMessage(), 'Duplicate') !== false) {
                    $errorMessage .= 'Bu sipariş zaten kaydedilmiş.';
                }
                // Diğer SQL hataları
                else {
                    $errorMessage .= 'Veritabanı işlemi sırasında bir hata oluştu. Lütfen yöneticinize bildirin.';
                }
            } else {
                $errorMessage .= 'Beklenmeyen bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
            
            // JSON isteği ise JSON hata cevabı döndür
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error_details' => $e->getMessage()
                ], 500);
            }
            
            // Normal istek ise yönlendirme yap
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    public function getCartCount()
    {
        $cart = session()->get('cart', []);
        $totalQuantity = 0;
        $totalAmount = 0;
        
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $totalQuantity += $item['quantity'];
                $totalAmount += floatval($item['price']) * intval($item['quantity']);
            }
        }
        
        return response()->json([
            'cart_count' => $totalQuantity,
            'cart' => $cart,
            'total_amount' => number_format($totalAmount, 2)
        ]);
    }

    public function updateQuantity(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $cart = session()->get('cart', []);
            
            if (isset($cart[$id])) {
                $currentQuantity = $cart[$id]['quantity'];
                $newQuantity = $currentQuantity;
                
                if ($request->input('action') === 'increase') {
                    // Stok ve rezervasyon kontrolü
                    $product = Product::with('stock')->findOrFail($id);
                    
                    if (!$product->stock) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Bu ürün için stok bilgisi bulunamadı.'
                        ], 400);
                    }

                    // Aktif siparişlerdeki rezerve edilmiş ürünleri kontrol et
                    $reservedQuantity = OrderDetail::whereHas('order', function($query) {
                        $query->whereIn('status', ['bekliyor', 'hazirlaniyor']);
                    })->where('product_id', $id)->sum('quantity');

                    // Gerçek stok miktarı = Stok - Rezerve edilmiş miktar
                    $availableStock = $product->stock->quantity - $reservedQuantity;
                    
                    if ($availableStock <= 0 || $availableStock < ($currentQuantity + 1)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Yeterli stok bulunmamaktadır.'
                        ], 400);
                    }
                    
                    $newQuantity = $currentQuantity + 1;
                    $cart[$id]['quantity'] = $newQuantity;
                    
                    // Rezervasyonu güncelle
                    $reservations = session()->get('reserved_stock', []);
                    $found = false;
                    foreach ($reservations as $key => $reservation) {
                        if ($reservation['product_id'] == $id) {
                            $reservations[$key]['quantity'] += 1;
                            $reservations[$key]['timestamp'] = now();
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        $reservations[] = [
                            'product_id' => $id,
                            'quantity' => 1,
                            'timestamp' => now()
                        ];
                    }
                    
                    session()->put('reserved_stock', $reservations);
                    
                } else if ($request->input('action') === 'decrease') {
                    $newQuantity = $currentQuantity - 1;
                    
                    // Rezervasyonu güncelle
                    $reservations = session()->get('reserved_stock', []);
                    foreach ($reservations as $key => $reservation) {
                        if ($reservation['product_id'] == $id) {
                            $reservations[$key]['quantity'] -= 1;
                            $reservations[$key]['timestamp'] = now();
                            
                            // Eğer rezervasyon 0'a düştüyse kaldır
                            if ($reservations[$key]['quantity'] <= 0) {
                                unset($reservations[$key]);
                            }
                            break;
                        }
                    }
                    session()->put('reserved_stock', array_values($reservations));
                    
                    // Eğer miktar 0'a düştüyse ürünü sepetten kaldır
                    if ($newQuantity <= 0) {
                        unset($cart[$id]);
                    } else {
                        $cart[$id]['quantity'] = $newQuantity;
                    }
                }
                
                session()->put('cart', $cart);
                
                // Toplam tutarı hesapla
                $cartTotal = 0;
                $totalQuantity = 0;
                foreach ($cart as $item) {
                    $cartTotal += $item['price'] * $item['quantity'];
                    $totalQuantity += $item['quantity'];
                }
                
                // Eğer ürün hala sepette varsa toplam tutarını hesapla
                $itemTotal = isset($cart[$id]) ? $cart[$id]['price'] * $cart[$id]['quantity'] : 0;
                $quantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'quantity' => $quantity,
                    'total' => $itemTotal,
                    'cart_total' => $cartTotal,
                    'cart_count' => $totalQuantity
                ]);
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Miktar güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Sepeti temizle ve rezervasyonları iptal et
    public function clearCart()
    {
        try {
            DB::beginTransaction();
            
            // Sepeti ve rezervasyonları temizle
            session()->forget('cart');
            session()->forget('reserved_stock');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'cart_count' => 0
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Sepet temizlenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}