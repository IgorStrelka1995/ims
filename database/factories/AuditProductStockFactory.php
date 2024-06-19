<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AuditProductStockFactory extends Factory
{
    protected $model = Audit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::all()->random();

        $random = rand(0,1) == 1;

        $quantityChange = $random ? $product->stock + 1 : $product->stock - 1;
        $action = $random ? Audit::STOCK_IN_ACTION : Audit::STOCK_OUT_ACTION;

        $product->stock = $quantityChange;
        $product->save();

        return [
            'action' => $action,
            'product_id' => $product->id
        ];
    }
}
