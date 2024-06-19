<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const ITEMS_PER_PAGE = 50;

    const PRODUCT_STORE_PERMISSION = 'product-create';
    const PRODUCT_VIEW_PERMISSION = 'product-view';
    const PRODUCT_UPDATE_PERMISSION = 'product-update';
    const PRODUCT_DESTROY_PERMISSION = 'product-destroy';

    protected $guarded = [];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
