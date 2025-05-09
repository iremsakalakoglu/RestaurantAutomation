<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the user associated with the customer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}


