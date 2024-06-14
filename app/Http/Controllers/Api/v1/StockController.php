<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\StockIn;
use App\Events\StockOut;
use App\Http\Controllers\Controller;
use App\Http\Resources\StockCollection;
use App\Http\Resources\StockResource;
use App\Models\Audit;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $stocks = QueryBuilder::for(Stock::class)
            ->allowedFilters([
                AllowedFilter::exact('product_id')
            ])
            ->paginate($request->input('per_page', Stock::ITEMS_PER_PAGE));

        return new StockCollection($stocks);
    }

    /**
     * Display the specified resource.
     */
    public function show(Stock $stock)
    {
        return StockResource::make($stock);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return StockResource
     */
    public function in(Request $request, Product $product)
    {
        // Add validation rules

        $data = $request->only(['quantity', 'user_id']);

        $stock = Stock::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'type' => Stock::STOCK_IN
        ]);

        event(new StockIn($product, $stock, $data['user_id']));

        return StockResource::make($stock);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return StockResource
     */
    public function out(Request $request, Product $product)
    {
        // Add validation rules

        $data = $request->only(['quantity', 'user_id']);

        $stock = Stock::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'type' => Stock::STOCK_OUT
        ]);

        event(new StockOut($product, $stock, $data['user_id']));

        return StockResource::make($stock);
    }
}
