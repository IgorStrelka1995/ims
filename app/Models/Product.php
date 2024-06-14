<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    const ITEMS_PER_PAGE = 50;

    protected $guarded = ['user_id'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
