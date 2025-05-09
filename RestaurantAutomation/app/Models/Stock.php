<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'unit',
        'supplier',
        'manufacturer_id',
        'purchase_price',
        'sale_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
