<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $sku = $this->generateUniqueSku($name);

        return [
            'sku' => $sku,
            'name' => $name,
            'description' => $this->faker->text(),
            'price' => $this->faker->randomFloat(2, 1, 800),
            'stock' => $this->faker->numberBetween(10, 100)
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            Audit::factory()->create([
                'action' => Audit::PRODUCT_ADD_ACTION,
                'product_id' => $product->id
            ]);
        });
    }

    private function generateUniqueSku($name)
    {
        $baseSku = Str::slug($name);
        $sku = $baseSku;
        $count = 1;

        while (Product::where('sku', $sku)->exists()) {
            $count++;
            $sku = $baseSku . '-' . $count;
        }

        return $sku;
    }
}
