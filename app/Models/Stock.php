<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    const STOCK_IN = 'In';
    const STOCK_OUT = 'Out';
    const ITEMS_PER_PAGE = 100;

    protected $guarded = ['user_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
