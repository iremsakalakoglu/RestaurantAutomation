<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'table_number',
        'qr_code',
        'status',
        'capacity',
        'waiter_id'
    ];

    protected $casts = [
        'capacity' => 'integer'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function currentOrder()
    {
        return $this->hasOne(Order::class)->whereIn('status', ['bekliyor', 'hazirlaniyor', 'tamamlandi'])
            ->latest();
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }
}
