<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
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

        $product->stock = $quantityChange;
        $product->save();

        return [
            'product_id' => $product->id,
            'quantity' => $quantityChange,
            'type' => $random ? 'In' : 'Out'
        ];
    }
}
