<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    const PRODUCT_ADD_ACTION = 'product_add';
    const PRODUCT_UPDATE_ACTION = 'product_update';

    const STOCK_IN_ACTION = 'stock_in';
    const STOCK_OUT_ACTION = 'stock_out';
    const STOCK_ADJUSTMENT_ACTION = 'stock_adjustment';

    const AUDIT_VIEW_PERMISSION = 'audit-view';

    const ITEMS_PER_PAGE = 100;

    protected $table = 'audit_logs';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
