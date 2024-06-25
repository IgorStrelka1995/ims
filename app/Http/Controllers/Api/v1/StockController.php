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
        $this->authorize('viewAny', Stock::class);

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
        $this->authorize('view', Stock::class);

        return StockResource::make($stock);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return StockResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function in(Request $request, Product $product)
    {
        $this->authorize('in', Stock::class);

        // Add validation rules

        $data = $request->only(['quantity']);

        $stock = Stock::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'type' => Stock::STOCK_IN
        ]);

        event(new StockIn($product, $stock));

        return StockResource::make($stock);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return StockResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function out(Request $request, Product $product)
    {
        $this->authorize('out', Stock::class);

        // Add validation rules

        $data = $request->only(['quantity']);

        $stock = Stock::create([
            'product_id' => $product->id,
            'quantity' => $data['quantity'],
            'type' => Stock::STOCK_OUT
        ]);

        event(new StockOut($product, $stock));

        return StockResource::make($stock);
    }
}
