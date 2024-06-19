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

    const STOCK_VIEW_PERMISSION = 'stock-view';
    const STOCK_IN_PERMISSION = 'stock-in';
    const STOCK_OUT_PERMISSION = 'stock-out';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
