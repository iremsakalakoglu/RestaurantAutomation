<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id', 
        'name', 
        'description', 
        'price', 
        'image', 
        'stock_quantity', 
        'stock_tracking',
        'barcode',
        'manufacturer_id'
    ];

    protected $appends = ['current_price'];

    public function getCurrentPriceAttribute()
    {
        $stock = $this->stock;
        return $stock ? $stock->sale_price : 0;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Ürünün üreticisi
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function stockMovements()
    {
        return $this->hasManyThrough(StockMovement::class, Stock::class);
    }
    
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function latestStock()
    {
        return $this->hasOne(Stock::class)->latest();
    }

    public function currentStock()
    {
        return $this->hasOne(Stock::class)->select('id', 'product_id', 'quantity', 'unit');
    }
}
