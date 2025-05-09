<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    private function checkAdminAccess()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('menu');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        // Kategorileri ve her kategorideki ürün sayısını al
        $categories = Category::withCount('products')->paginate(9);
        return view('Dashboard.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'icon_type' => 'required|in:default,custom',
                'icon' => $request->icon_type === 'custom' ? 'required|image|max:2048' : 'nullable|string'
            ]);

            $category = new Category();
            $category->name = $request->name;
            $category->icon_type = $request->icon_type;

            if ($request->icon_type === 'custom' && $request->hasFile('icon')) {
                $file = $request->file('icon');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('category-icons', $fileName, 'public');
                $category->icon = $path;
                
                // Log dosya bilgilerini
                \Log::info('File Upload Details:', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_name' => $fileName,
                    'path' => $path,
                    'full_path' => Storage::disk('public')->path($path),
                    'exists' => Storage::disk('public')->exists($path)
                ]);
            } else {
                $category->icon = $request->icon;
            }

            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla oluşturuldu.',
                'category' => $category
            ]);

        } catch (\Exception $e) {
            \Log::error('Category Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kategori eklenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $category = Category::findOrFail($id);
            return response()->json([
                'success' => true,
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $category = Category::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
                'icon_type' => 'required|in:default,custom',
                'icon' => $request->icon_type === 'custom' ? 'nullable|image|max:2048' : 'nullable|string'
            ]);

            $category->name = $request->name;
            $category->icon_type = $request->icon_type;

            if ($request->icon_type === 'custom') {
                if ($request->hasFile('icon')) {
                    // Eski ikonu sil
                    if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                        Storage::disk('public')->delete($category->icon);
                    }
                    // Yeni ikonu kaydet
                    $path = $request->file('icon')->store('category-icons', 'public');
                    $category->icon = $path;
                }
            } else {
                // Eğer önceden custom ikon varsa sil
                if ($category->icon_type === 'custom' && $category->icon && Storage::disk('public')->exists($category->icon)) {
                    Storage::disk('public')->delete($category->icon);
                }
                $category->icon = $request->icon;
            }

            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla güncellendi.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $category = Category::findOrFail($id);
            
            // Eğer custom ikon varsa sil
            if ($category->icon_type === 'custom' && $category->icon && Storage::disk('public')->exists($category->icon)) {
                Storage::disk('public')->delete($category->icon);
            }

            // Kategoriye bağlı ürünleri kontrol et
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu kategoriye ait ürünler bulunmaktadır. Önce ürünleri silmelisiniz.'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori başarıyla silindi.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Kategori silme hatası: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Kategori silinirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        if ($redirect = $this->checkAdminAccess()) {
            return $redirect;
        }

        try {
            $category = Category::findOrFail($id);
            return response()->json([
                'success' => true,
                'category' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori bulunamadı'
            ], 404);
        }
    }
} 