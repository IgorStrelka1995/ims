<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;

    const PRODUCT_ADD_ACTION = 'product_add';
    const PRODUCT_UPDATE_ACTION = 'product_update';
    const PRODUCT_DELETE_ACTION = 'product_delete';

    const STOCK_IN_ACTION = 'stock_in';
    const STOCK_OUT_ACTION = 'stock_out';

    protected $table = 'audit_logs';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
