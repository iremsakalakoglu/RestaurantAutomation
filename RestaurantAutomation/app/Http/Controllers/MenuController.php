<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Masa bilgisini öncelikle session'dan al
        $tableId = Session::get('table_id');

        // Eğer session'da masa bilgisi yoksa VE URL'de varsa, URL'den al ve session'a kaydet
        if (empty($tableId) && $request->has('table')) {
            $tableId = $request->input('table');
            Session::put('table_id', $tableId);
        }

        $table = null;
        $error = null;
        
        // Eğer masa ID'si belirlenmişse, masa bilgilerini çek
        if ($tableId) {
            $table = Table::findOrFail($tableId);
            
            // Masa durumunu kontrol et
            if ($table->status === 'dolu') {
                // Masa dolu ise, aktif siparişi kontrol et
                $activeOrder = Order::where('table_id', $tableId)
                    ->whereIn('status', ['bekliyor', 'hazirlaniyor', 'hazir'])
                    ->first();
                
                if ($activeOrder) {
                    $error = 'Bu masa şu anda kullanımda. Lütfen başka bir masa seçin.';
                    // Sadece aktif sipariş varsa sepeti temizle (mantıksız olabilir, sepet masa ile ilişkilendirilmiş olabilir)
                    // Şu anki mantığa göre dolu masada yeni sipariş başlatılamaz gibi duruyor, sepeti temizlemek mantıklı olabilir.
                    if ($activeOrder->status !== 'tamamlandi' && $activeOrder->status !== 'iptal') {
                         session()->forget('cart');
                         session()->forget('reserved_stock');
                    }

                } else {
                     // Masa dolu ama aktif sipariş yoksa, sepeti ve reserved_stock'ı temizle (belki önceki kullanımdan kalmıştır)
                     session()->forget('cart');
                     session()->forget('reserved_stock');
                }
            } else {
                 // Masa boşsa, sepeti ve reserved_stock'ı temizle (yeni sipariş için hazır)
                 session()->forget('cart');
                 session()->forget('reserved_stock');
            }
        } else {
             // Masa ID'si hiç yoksa (ne session'da ne URL'de), sepeti ve reserved_stock'ı temizle
             session()->forget('cart');
             session()->forget('reserved_stock');
        }
        
        // Get categories and their products
        $categories = Category::with(['products.stock'])->get();

        // Send categories, products, table and error to the view
        return view('menu', compact('categories', 'table', 'error', 'tableId')); // tableId'yi de view'a gönderelim
    }
} 