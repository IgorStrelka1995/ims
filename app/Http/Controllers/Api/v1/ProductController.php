<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('sku'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('price'),
                AllowedFilter::exact('stock'),
            ])
            ->allowedIncludes(['stocks'])
            ->paginate($request->input('per_page', Product::ITEMS_PER_PAGE));

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->only(['sku', 'name', 'description', 'price', 'stock']);

        $product = Product::create($data);

        event(new ProductCreated($product));

        return ProductResource::make($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $this->authorize('view', Product::class);

        return ProductResource::make($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', Product::class);

        $data = $request->only(['sku', 'name', 'description', 'price', 'stock']);

        $previousStock = $product->stock;

        $product->update($data);

        event(new ProductUpdated($product, $previousStock));

        return ProductResource::make($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', Product::class);

        $product->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
