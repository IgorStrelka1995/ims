<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AuditProductUpdateFactory extends Factory
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

        $priceChange = $random ? $product->price + 1 : $product->price - 1;

        $product->price = $priceChange;
        $product->save();

        return [
            'action' => Audit::PRODUCT_UPDATE_ACTION,
            'product_id' => $product->id
        ];
    }
}
