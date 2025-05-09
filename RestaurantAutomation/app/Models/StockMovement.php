<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
    protected $fillable = [
        'stock_id',
        'order_id',
        'type',
        'quantity',
        'unit',
        'description',
        'supplier',
        'manufacturer',
        'purchase_price',
        'sale_price',
        'arrival_date'
    ];

    protected $casts = [
        'arrival_date' => 'datetime',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2'
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
