<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use App\Models\Order;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Get table information if provided
        $tableId = $request->query('table');
        $table = null;
        $error = null;
        
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
                    // Sadece aktif sipariş varsa sepeti temizle
                    if ($activeOrder->status !== 'tamamlandi' && $activeOrder->status !== 'iptal') {
                        session()->forget('cart');
                        session()->forget('reserved_stock');
                    }
                }
            }
        }
        
        // Get categories and their products
        $categories = Category::with(['products.stock'])->get();

        // Send categories, products, and table to the view
        return view('menu', compact('categories', 'table', 'error'));
    }
} 