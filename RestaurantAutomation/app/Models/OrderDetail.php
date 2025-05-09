<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'is_delivered',
        'is_paid'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'is_delivered' => 'boolean',
        'is_paid' => 'boolean'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
