<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'stock_id', 'quantity', 'unit'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
