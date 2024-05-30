<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    const STOCK_IN = 'In';
    const STOCK_OUT = 'Out';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
