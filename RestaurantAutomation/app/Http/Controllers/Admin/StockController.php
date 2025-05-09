<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    private function checkAdminAccess()
    {
        if (!auth()->check()) {
            \Log::info('Kullanıcı giriş yapmamış');
            return redirect()->route('auth.login');
        }

        $userRole = auth()->user()->role;
        \Log::info('Kullanıcı rolü: ' . $userRole);

        if (!in_array($userRole, ['admin', 'kitchen'])) {
            \Log::info('Yetkisiz erişim denemesi');
            return redirect()->route('menu')->with('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return null;
    }

    private function getStockStatistics()
    {
        try {
            // Son 7 günlük stok hareketleri
            $lastSevenDays = StockMovement::selectRaw('DATE(created_at) as date, type, COUNT(*) as count')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('date', 'type')
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(function ($group) {
                    return [
                        'giris' => $group->where('type', 'giris')->first()->count ?? 0,
                        'cikis' => $group->where('type', 'cikis')->first()->count ?? 0
                    ];
                });

            // Kategori bazında stok dağılımı
            $categoryDistribution = DB::table('stocks')
                ->join('products', 'stocks.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('SUM(stocks.quantity) as total_quantity'))
                ->groupBy('categories.id', 'categories.name')
                ->get();

            // Düşük stok uyarıları
            $lowStockCount = Stock::where('quantity', '<=', 5)->count();

            // Toplam stok değeri
            $totalStockValue = Stock::sum(DB::raw('quantity * purchase_price'));

            // Son ay içindeki toplam giriş-çıkış
            $monthlyMovements = StockMovement::selectRaw('type, SUM(quantity) as total_quantity')
                ->whereMonth('created_at', now()->month)
                ->groupBy('type')
                ->get()
                ->pluck('total_quantity', 'type');

            return [
                'lastSevenDays' => $lastSevenDays,
                'categoryDistribution' => $categoryDistribution,
                'lowStockCount' => $lowStockCount,
                'totalStockValue' => $totalStockValue,
                'monthlyMovements' => $monthlyMovements
            ];
        } catch (\Exception $e) {
            \Log::error('Stok istatistikleri hesaplanırken hata: ' . $e->getMessage());
            return null;
        }
    }

    public function index()
    {
        \Log::info('Stok yönetimi sayfası açılıyor');
        
        if ($redirect = $this->checkAdminAccess()) {
            \Log::info('Erişim reddedildi');
            return $redirect;
        }

        try {
            // Ürünleri ve stok bilgilerini al
            $products = Product::with(['stock' => function($query) {
                $query->withCount('stockMovements');
            }, 'category'])->get();
            \Log::info('Ürün sayısı: ' . $products->count());
            
            // Kategorileri al
            $categories = \App\Models\Category::all();
            \Log::info('Kategori sayısı: ' . $categories->count());
            
            // Tablo için stok hareketlerini al
            $tableMovements = StockMovement::with(['stock.product.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            // Grafik için son 7 günlük stok hareketlerini al
            $chartMovements = StockMovement::whereDate('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function($movement) {
                    return $movement->created_at->format('d.m.Y');
                })
                ->map(function($group) {
                    return [
                        'giris' => $group->where('type', 'giris')->sum('quantity'),
                        'cikis' => $group->where('type', 'cikis')->sum('quantity')
                    ];
                });

            // Düşük stoklu ürünleri bul
            $lowStockProducts = Stock::with('product')
                ->where('quantity', '<=', 5)
                ->get();
            \Log::info('Düşük stoklu ürün sayısı: ' . $lowStockProducts->count());

            return view('Dashboard.inventory', compact(
                'products', 
                'tableMovements',
                'chartMovements',
                'lowStockProducts', 
                'categories'
            ));
        } catch (\Exception $e) {
            \Log::error('Stok yönetimi hatası: ' . $e->getMessage());
            return back()->with('error', 'Stok bilgileri yüklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function updateStock(Request $request, $stockId)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim'], 403);
        }

        try {
            $request->validate([
                'type' => 'required|in:giris,cikis',
                'quantity' => 'required|numeric|min:1',
                'description' => 'nullable|string|max:255',
                'purchase_price' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0'
            ]);

            DB::beginTransaction();

            $stock = Stock::findOrFail($stockId);

            // Stok miktarını güncelle
            if ($request->type === 'giris') {
                $stock->quantity += $request->quantity;
            } else {
                if ($stock->quantity < $request->quantity) {
                    throw new \Exception('Yetersiz stok miktarı');
                }
                $stock->quantity -= $request->quantity;
            }

            // Fiyat bilgilerini güncelle (eğer gelirse)
            if ($request->has('purchase_price') && $request->purchase_price !== null) {
                $stock->purchase_price = $request->purchase_price;
            }
            
            if ($request->has('sale_price') && $request->sale_price !== null) {
                $stock->sale_price = $request->sale_price;
            }

            $stock->save();

            // Stok hareketi oluştur
            StockMovement::create([
                'stock_id' => $stock->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'description' => $request->description ?? ($request->type === 'giris' ? 'Stok eklendi' : 'Stok çıkarıldı'),
                'purchase_price' => $request->purchase_price ?? $stock->purchase_price,
                'sale_price' => $request->sale_price ?? $stock->sale_price
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stok başarıyla güncellendi',
                'new_quantity' => $stock->quantity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Stok güncelleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStockMovements($stockId)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim'], 403);
        }

        try {
            $stock = Stock::findOrFail($stockId);
            
            $movements = StockMovement::where('stock_id', $stockId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($movement) {
                    return [
                        'type' => $movement->type,
                        'quantity' => $movement->quantity,
                        'description' => $movement->description,
                        'purchase_price' => $movement->purchase_price,
                        'sale_price' => $movement->sale_price,
                        'created_at' => $movement->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'movements' => $movements
            ]);
        } catch (\Exception $e) {
            \Log::error('Stok hareketleri hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok hareketleri alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllStockMovements(Request $request)
    {
        try {
            $query = StockMovement::with(['stock.product.category']);

            // Ürün filtresi
            if ($request->has('product_id') && $request->product_id) {
                $query->whereHas('stock', function($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }

            // Hareket tipi filtresi
            if ($request->has('type') && $request->type) {
                $query->where('type', $request->type);
            }

            // Tedarikçi filtresi
            if ($request->has('supplier') && $request->supplier) {
                $query->whereHas('stock', function($q) use ($request) {
                    $q->where('supplier', 'LIKE', '%' . $request->supplier . '%');
                });
            }

            // Üretici filtresi
            if ($request->has('manufacturer') && $request->manufacturer) {
                $query->whereHas('stock', function($q) use ($request) {
                    $q->where('manufacturer', 'LIKE', '%' . $request->manufacturer . '%');
                });
            }

            // Kategori filtresi
            if ($request->has('category_id') && $request->category_id) {
                $query->whereHas('stock.product', function($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            // Fiyat aralığı filtresi (Alış Fiyatı)
            if ($request->has('min_purchase_price')) {
                $query->where('purchase_price', '>=', $request->min_purchase_price);
            }
            if ($request->has('max_purchase_price')) {
                $query->where('purchase_price', '<=', $request->max_purchase_price);
            }

            // Fiyat aralığı filtresi (Satış Fiyatı)
            if ($request->has('min_sale_price')) {
                $query->where('sale_price', '>=', $request->min_sale_price);
            }
            if ($request->has('max_sale_price')) {
                $query->where('sale_price', '<=', $request->max_sale_price);
            }

            // Miktar aralığı filtresi
            if ($request->has('min_quantity')) {
                $query->where('quantity', '>=', $request->min_quantity);
            }
            if ($request->has('max_quantity')) {
                $query->where('quantity', '<=', $request->max_quantity);
            }

            // Tarih aralığı filtresi
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Geliş tarihi aralığı filtresi
            if ($request->has('arrival_date_from') && $request->arrival_date_from) {
                $query->whereDate('arrival_date', '>=', $request->arrival_date_from);
            }
            if ($request->has('arrival_date_to') && $request->arrival_date_to) {
                $query->whereDate('arrival_date', '<=', $request->arrival_date_to);
            }

            // Sıralama
            $sortField = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $allowedSortFields = ['created_at', 'arrival_date', 'quantity', 'purchase_price', 'sale_price'];
            
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Sayfalama
            $perPage = 15; // Sabit 15 kayıt
            $movements = $query->paginate($perPage);

            // Null kontrolü ekle
            $movements->getCollection()->transform(function ($movement) {
                if ($movement->stock && $movement->stock->product) {
                    return [
                        'id' => $movement->id,
                        'created_at' => $movement->created_at,
                        'arrival_date' => $movement->arrival_date,
                        'type' => $movement->type,
                        'quantity' => $movement->quantity,
                        'description' => $movement->description,
                        'purchase_price' => $movement->purchase_price,
                        'sale_price' => $movement->sale_price,
                        'stock' => [
                            'id' => $movement->stock->id,
                            'unit' => $movement->stock->unit,
                            'supplier' => $movement->stock->supplier,
                            'manufacturer' => $movement->stock->manufacturer,
                            'product' => [
                                'name' => $movement->stock->product->name,
                                'category' => [
                                    'name' => $movement->stock->product->category->name ?? 'Kategorisiz'
                                ]
                            ]
                        ]
                    ];
                }
                return null;
            });

            // Null değerleri filtrele
            $movements->setCollection($movements->getCollection()->filter());

            return response()->json([
                'success' => true,
                'movements' => $movements
            ]);
        } catch (\Exception $e) {
            \Log::error('Stok hareketleri alınırken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok hareketleri alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Yeni stok kaydı oluştur.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Stok işlemi başlatıldı: ' . json_encode($request->all()));
            
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'unit' => 'required|string',
                'quantity' => 'required|numeric|min:0',
                'type' => 'nullable|string|in:giris,cikis',
                'description' => 'nullable|string',
                'supplier' => 'nullable|string',
                'manufacturer' => 'nullable|string',
                'purchase_price' => 'nullable|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0',
                'arrival_date' => 'nullable|date',
            ]);

            $productId = $validated['product_id'];
            \Log::info('Ürün ID: ' . $productId);
            
            $existingStock = Stock::where('product_id', $productId)->first();
            \Log::info('Mevcut stok: ' . ($existingStock ? 'Bulundu (ID:' . $existingStock->id . ')' : 'Bulunamadı'));
            
            // İşlem tipini kontrol et
            $type = $validated['type'] ?? 'giris';
            \Log::info('İşlem tipi: ' . $type);
            
            if ($existingStock) {
                // Eğer zaten stok kaydı varsa, stok hareketi oluştur
                $movementQuantity = $validated['quantity'];
                
                // Eğer çıkış işlemiyse, mevcut stoktan düşür
                if ($type === 'cikis') {
                    if ($existingStock->quantity < $movementQuantity) {
                        \Log::warning('Yetersiz stok: Mevcut: ' . $existingStock->quantity . ', İstenen: ' . $movementQuantity);
                        return response()->json([
                            'success' => false,
                            'message' => 'Yeterli stok bulunmamaktadır.'
                        ], 400);
                    }
                    $existingStock->quantity -= $movementQuantity;
                } else {
                    $existingStock->quantity += $movementQuantity;
                }

                // Supplier ve manufacturer bilgilerini güncelle (eğer gönderilmişse)
                if (!empty($validated['supplier'])) {
                    $existingStock->supplier = $validated['supplier'];
                }
                
                if (!empty($validated['manufacturer'])) {
                    $existingStock->manufacturer = $validated['manufacturer'];
                }
                
                // Fiyat bilgilerini güncelle (eğer gönderilmişse)
                if (isset($validated['purchase_price'])) {
                    $existingStock->purchase_price = $validated['purchase_price'];
                }
                
                if (isset($validated['sale_price'])) {
                    $existingStock->sale_price = $validated['sale_price'];
                }
                
                \Log::info('Stok güncellenecek: ' . json_encode([
                    'quantity' => $existingStock->quantity,
                    'supplier' => $existingStock->supplier,
                    'manufacturer' => $existingStock->manufacturer,
                    'purchase_price' => $existingStock->purchase_price,
                    'sale_price' => $existingStock->sale_price
                ]));
                
                try {
                    $existingStock->save();
                    \Log::info('Stok başarıyla güncellendi');
                } catch (\Exception $e) {
                    \Log::error('Stok güncellenirken hata: ' . $e->getMessage());
                    throw $e;
                }
                
                // Stok hareketi oluştur
                try {
                    $stockMovement = StockMovement::create([
                        'stock_id' => $existingStock->id,
                        'type' => $type,
                        'quantity' => $movementQuantity,
                        'description' => $validated['description'] ?? ($type === 'giris' ? 'Stok eklendi' : 'Stok çıkarıldı'),
                        'purchase_price' => $validated['purchase_price'] ?? null,
                        'sale_price' => $validated['sale_price'] ?? null,
                        'arrival_date' => $validated['arrival_date'] ?? null,
                    ]);
                    \Log::info('Stok hareketi oluşturuldu: ' . $stockMovement->id);
                } catch (\Exception $e) {
                    \Log::error('Stok hareketi oluşturulurken hata: ' . $e->getMessage());
                    throw $e;
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $type === 'giris' 
                        ? 'Stok başarıyla güncellendi ve giriş hareketi kaydedildi.' 
                        : 'Stok başarıyla güncellendi ve çıkış hareketi kaydedildi.'
                ]);
            } else {
                // İlk kez stok oluşturuluyorsa
                if ($type === 'cikis') {
                    \Log::warning('Henüz oluşturulmamış stoktan çıkış yapılmaya çalışıldı');
                    return response()->json([
                        'success' => false,
                        'message' => 'Henüz oluşturulmamış bir stoktan çıkış yapamazsınız.'
                    ], 400);
                }
                
                // Yeni stok kaydı oluştur
                \Log::info('Yeni stok kaydı oluşturulacak: ' . json_encode([
                    'product_id' => $productId,
                    'unit' => $validated['unit'],
                    'quantity' => $validated['quantity'],
                    'supplier' => $validated['supplier'] ?? null,
                    'manufacturer' => $validated['manufacturer'] ?? null,
                    'purchase_price' => $validated['purchase_price'] ?? null,
                    'sale_price' => $validated['sale_price'] ?? null,
                ]));
                
                try {
                    $stock = Stock::create([
                        'product_id' => $productId,
                        'unit' => $validated['unit'],
                        'quantity' => $validated['quantity'],
                        'supplier' => $validated['supplier'] ?? null,
                        'manufacturer' => $validated['manufacturer'] ?? null,
                        'purchase_price' => $validated['purchase_price'] ?? null,
                        'sale_price' => $validated['sale_price'] ?? null,
                    ]);
                    \Log::info('Yeni stok kaydı oluşturuldu: ' . $stock->id);
                } catch (\Exception $e) {
                    \Log::error('Stok oluşturulurken hata: ' . $e->getMessage());
                    throw $e;
                }
                
                // Stok giriş hareketi oluştur
                try {
                    $stockMovement = StockMovement::create([
                        'stock_id' => $stock->id,
                        'type' => 'giris',
                        'quantity' => $validated['quantity'],
                        'description' => $validated['description'] ?? 'İlk stok kaydı',
                        'purchase_price' => $validated['purchase_price'] ?? null,
                        'sale_price' => $validated['sale_price'] ?? null,
                        'arrival_date' => $validated['arrival_date'] ?? null,
                    ]);
                    \Log::info('İlk stok hareketi oluşturuldu: ' . $stockMovement->id);
                } catch (\Exception $e) {
                    \Log::error('İlk stok hareketi oluşturulurken hata: ' . $e->getMessage());
                    throw $e;
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Stok başarıyla oluşturuldu.'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            \Log::error('Stok validasyon hatası: ' . implode(', ', $errors));
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası: ' . implode(', ', $errors),
                'errors' => $errors
            ], 422);
        } catch (\PDOException $e) {
            $errorMessage = $e->getMessage();
            \Log::error('Veritabanı hatası: ' . $errorMessage);
            
            // Daha anlamlı hata mesajları için hata türlerini kontrol et
            if (strpos($errorMessage, 'Unknown column') !== false) {
                // Kolon bulunamadı hatası
                preg_match("/Unknown column '(.+?)' in/", $errorMessage, $matches);
                $column = $matches[1] ?? 'bilinmeyen alan';
                return response()->json([
                    'success' => false,
                    'message' => 'Veritabanı hatası: "' . $column . '" alanı veritabanında bulunamadı.',
                    'error_details' => $errorMessage
                ], 500);
            } 
            else if (strpos($errorMessage, "doesn't have a default value") !== false) {
                // Default değer olmayan alan hatası
                preg_match("/Column '(.+?)' cannot/", $errorMessage, $matches);
                $column = $matches[1] ?? 'bilinmeyen alan';
                return response()->json([
                    'success' => false,
                    'message' => 'Veritabanı hatası: "' . $column . '" alanı için bir değer girmelisiniz.',
                    'error_details' => $errorMessage
                ], 500);
            }
            else if (strpos($errorMessage, 'SQLSTATE') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veritabanı bağlantı hatası. Lütfen veritabanı ayarlarınızı kontrol edin.',
                    'error_details' => $errorMessage
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Veritabanı hatası oluştu. Lütfen daha sonra tekrar deneyin.',
                'error_details' => $errorMessage
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Stok ekleme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Stok işlemi yapılırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProducts()
    {
        if ($redirect = $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim'], 403);
        }

        try {
            $products = Product::with('stock')
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'barcode' => $product->barcode,
                        'has_stock' => $product->stock !== null,
                        'stock' => $product->stock ? [
                            'id' => $product->stock->id,
                            'unit' => $product->stock->unit,
                            'quantity' => $product->stock->quantity,
                            'supplier' => $product->stock->supplier,
                            'manufacturer' => $product->stock->manufacturer,
                            'purchase_price' => $product->stock->purchase_price,
                            'sale_price' => $product->stock->sale_price
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $products
            ]);
        } catch (\Exception $e) {
            \Log::error('Ürün listesi hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ürünler alınırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics()
    {
        if ($redirect = $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim'], 403);
        }

        try {
            $statistics = $this->getStockStatistics();
            
            if ($statistics === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'İstatistikler hesaplanırken bir hata oluştu.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);
        } catch (\Exception $e) {
            \Log::error('İstatistik hesaplama hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'İstatistikler hesaplanırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchByBarcode($barcode)
    {
        $product = Product::with('stock')
            ->where('barcode', $barcode)
            ->first();

        return response()->json([
            'success' => !is_null($product),
            'product' => $product
        ]);
    }

    /**
     * Üreticileri listele
     */
    public function getManufacturers()
    {
        try {
            $manufacturers = \App\Models\Manufacturer::orderBy('name')
                ->get()
                ->map(function ($manufacturer) {
                    return [
                        'id' => $manufacturer->id,
                        'name' => $manufacturer->name,
                        'contact_person' => $manufacturer->contact_person,
                        'phone' => $manufacturer->phone,
                        'email' => $manufacturer->email,
                        'address' => $manufacturer->address,
                        'is_active' => $manufacturer->is_active,
                        'is_default' => $manufacturer->created_at <= now()->subMinutes(5) // 5 dakikadan eski kayıtları varsayılan olarak işaretle
                    ];
                });

            return response()->json([
                'success' => true,
                'manufacturers' => $manufacturers
            ]);
        } catch (\Exception $e) {
            \Log::error('Üreticiler listelenirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Üreticiler listelenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Yeni üretici ekle
     */
    public function storeManufacturer(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name',
                'contact_person' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'notes' => 'nullable|string'
            ]);

            $manufacturer = \App\Models\Manufacturer::create($validated);

            // Yeni eklenen üreticiyi varsayılan olarak işaretleme
            $manufacturer->is_default = false;
            
            return response()->json([
                'success' => true,
                'message' => 'Üretici başarıyla eklendi',
                'manufacturer' => $manufacturer
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Üretici eklenirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Üretici eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Üretici güncelle
     */
    public function updateManufacturer(Request $request, $id)
    {
        try {
            $manufacturer = \App\Models\Manufacturer::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:manufacturers,name,' . $id,
                'contact_person' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string',
                'notes' => 'nullable|string',
                'is_active' => 'boolean'
            ]);

            $manufacturer->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Üretici başarıyla güncellendi',
                'manufacturer' => $manufacturer
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Üretici bulunamadı'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Üretici güncellenirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Üretici güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Üretici sil
     */
    public function deleteManufacturer($id)
    {
        try {
            $manufacturer = \App\Models\Manufacturer::findOrFail($id);

            // Üreticiye bağlı ürünleri kontrol et
            if ($manufacturer->products()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu üretici ürünlerle ilişkili olduğu için silinemez. Önce ilişkili ürünleri güncelleyin.'
                ], 400);
            }

            $manufacturer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Üretici başarıyla silindi'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Üretici bulunamadı'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Üretici silinirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Üretici silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Varsayılan üreticileri yeniden yükle
     */
    public function reloadDefaultManufacturers()
    {
        try {
            $seeder = new \Database\Seeders\ManufacturerSeeder();
            $seeder->run();

            return response()->json([
                'success' => true,
                'message' => 'Varsayılan üreticiler başarıyla yeniden yüklendi'
            ]);
        } catch (\Exception $e) {
            \Log::error('Varsayılan üreticiler yüklenirken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Varsayılan üreticiler yüklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tek bir üreticiyi getir
     */
    public function getManufacturer($id)
    {
        try {
            $manufacturer = \App\Models\Manufacturer::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'manufacturer' => $manufacturer
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Üretici bulunamadı'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Üretici bilgileri alınırken hata: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Üretici bilgileri alınırken bir hata oluştu'
            ], 500);
        }
    }

    // --- Tedarikçi (Supplier) API ---
    public function getSuppliers()
    {
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        return response()->json(['success' => true, 'suppliers' => $suppliers]);
    }

    public function getSupplier($id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Tedarikçi bulunamadı'], 404);
        }
        return response()->json(['success' => true, 'supplier' => $supplier]);
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        $supplier = \App\Models\Supplier::create($validated);
        return response()->json([
            'success' => true,
            'supplier' => $supplier
        ]);
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Tedarikçi bulunamadı'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        $supplier->update($validated);
        return response()->json([
            'success' => true,
            'supplier' => $supplier
        ]);
    }

    public function deleteSupplier($id)
    {
        $supplier = \App\Models\Supplier::find($id);
        if (!$supplier) {
            return response()->json(['success' => false, 'message' => 'Tedarikçi bulunamadı'], 404);
        }
        $supplier->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tedarikçi silindi.'
        ]);
    }

    // Stok için tedarikçi güncelleme
    public function updateSupplierForStock(Request $request, $id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim'], 403);
        }
        $request->validate([
            'supplier' => 'nullable|string|max:255'
        ]);
        $stock = Stock::find($id);
        if (!$stock) {
            return response()->json(['success' => false, 'message' => 'Stok kaydı bulunamadı'], 404);
        }
        $stock->supplier = $request->supplier;
        $stock->save();
        return response()->json(['success' => true, 'message' => 'Tedarikçi başarıyla güncellendi.']);
    }
} 