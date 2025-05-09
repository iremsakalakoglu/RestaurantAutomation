<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'amount', 'refund_amount', 'payment_method', 'status', 'paid_at'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
