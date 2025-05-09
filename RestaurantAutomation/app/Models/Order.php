<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id', 
        'table_id', 
        'status',
        'created_at',
        'updated_at'
    ];
    
    // Define casts to ensure proper data type handling
    protected $casts = [
        'customer_id' => 'integer',
        'table_id' => 'integer',
        'status' => 'string',
    ];

    protected $with = ['orderDetails.product'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
