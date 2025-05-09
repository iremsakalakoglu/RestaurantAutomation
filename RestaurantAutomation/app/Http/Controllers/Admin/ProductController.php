<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function checkAdminAccess()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('menu');
        }
        return null;
    }

    public function index(Request $request)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        $query = Product::with(['category', 'latestStock', 'currentStock', 'manufacturer']);

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Üretici filtresi
        if ($request->filled('manufacturer')) {
            $query->where('manufacturer_id', $request->manufacturer);
        }

        // Fiyat aralığı filtresi
        if ($request->filled('min_price')) {
            $query->whereHas('latestStock', function($q) use ($request) {
                $q->where('sale_price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereHas('latestStock', function($q) use ($request) {
                $q->where('sale_price', '<=', $request->max_price);
            });
        }

        // Arama filtresi
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::all();
        $manufacturers = \App\Models\Manufacturer::where('is_active', true)->get();

        return view('Dashboard.products', compact('products', 'categories', 'manufacturers'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string',
                'barcode' => 'nullable|string|unique:products,barcode',
                'manufacturer_id' => 'nullable|exists:manufacturers,id'
            ]);

            $data = $request->only(['name', 'category_id', 'description', 'barcode', 'manufacturer_id']);
            $product = Product::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla eklendi',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $product = Product::with(['category', 'latestStock'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'description' => 'nullable|string',
                'barcode' => 'nullable|string|unique:products,barcode,' . $id,
                'manufacturer_id' => 'nullable|exists:manufacturers,id'
            ]);

            $product = Product::findOrFail($id);
            $data = $request->only(['name', 'category_id', 'description', 'barcode', 'manufacturer_id']);
            $product->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla güncellendi',
                'product' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $product = Product::findOrFail($id);
            
            // Ürün resmini sil
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla silindi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
} 