<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\StockIn;
use App\Events\StockOut;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockInRequest;
use App\Http\Requests\StockOutRequest;
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
     * @OA\Get(
     *     path="/api/v1/stocks",
     *     summary="List of all stocks",
     *     tags={"Stock"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="product_id", type="integer"),
     *                  @OA\Property(property="quantity", type="integer"),
     *                  @OA\Property(property="type", type="string"),
     *                  @OA\Property(property="created_at", type="string")
     *              )),
     *              example={
     *                  "data": {{
     *                      "id": 1,
     *                      "product_id": 12,
     *                      "quantity": 91,
     *                      "type": "In",
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  },
     *                  {
     *                      "id": 2,
     *                      "product_id": 44,
     *                      "quantity": 49,
     *                      "type": "Out",
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  }}
     *              }
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return StockCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * @OA\Get(
     *     path="/api/v1/stocks/{stock}",
     *     summary="Get single stock",
     *     tags={"Stock"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Stock id",
     *          in="path",
     *          name="stock",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="product_id", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="sku", type="string", example="lorem-ipsum"),
     *                      @OA\Property(property="name", type="string", example="name"),
     *                      @OA\Property(property="price", type="integer", example="10.20"),
     *                      @OA\Property(property="stock", type="integer", example=20),
     *                      @OA\Property(property="description", type="string", example="Voluptatum sequi odio sint dolorem consectetur nihil quasi."),
     *                      @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z")
     *                  ),
     *                  @OA\Property(property="quantity", type="string", example=41),
     *                  @OA\Property(property="type", type="string", example="In"),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z")
     *              )
     *         )
     *     )
     * )
     *
     * @param Stock $stock
     * @return StockResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Stock $stock)
    {
        $this->authorize('view', Stock::class);

        return StockResource::make($stock);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/stocks/in/{product}",
     *     summary="Stock In for product.",
     *     tags={"Stock"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\Parameter(
     *          description="Product id",
     *          in="path",
     *          name="product",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="quantity", type="integer"),
     *                 )
     *             },
     *              example={
     *                  "quantity": "100"
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="product", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="sku", type="string", example="lorem-ipsum"),
     *                      @OA\Property(property="name", type="string", example="name"),
     *                      @OA\Property(property="price", type="integer", example="10.20"),
     *                      @OA\Property(property="stock", type="integer", example=20),
     *                      @OA\Property(property="description", type="string", example="Voluptatum sequi odio sint dolorem consectetur nihil quasi."),
     *                      @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z")
     *                  ),
     *                  @OA\Property(property="quantity", type="string", example="100"),
     *                  @OA\Property(property="type", type="string", example="Out"),
     *                  @OA\Property(property="created_at", type="string"),
     *              )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Product $product
     * @return StockResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function in(StockInRequest $request, Product $product)
    {
        $this->authorize('in', Stock::class);

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
     * @OA\Post(
     *     path="/api/v1/stocks/out/{product}",
     *     summary="Stock Out for product.",
     *     tags={"Stock"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\Parameter(
     *          description="Product id",
     *          in="path",
     *          name="product",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="quantity", type="integer"),
     *                 )
     *             },
     *              example={
     *                  "quantity": "100"
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="product", type="object",
     *                      @OA\Property(property="id", type="integer", example="1"),
     *                      @OA\Property(property="sku", type="string", example="lorem-ipsum"),
     *                      @OA\Property(property="name", type="string", example="name"),
     *                      @OA\Property(property="price", type="integer", example="10.20"),
     *                      @OA\Property(property="stock", type="integer", example=20),
     *                      @OA\Property(property="description", type="string", example="Voluptatum sequi odio sint dolorem consectetur nihil quasi."),
     *                      @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z")
     *                  ),
     *                  @OA\Property(property="quantity", type="string"),
     *                  @OA\Property(property="type", type="string", example="Out"),
     *                  @OA\Property(property="created_at", type="string"),
     *              )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Product $product
     * @return StockResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function out(StockOutRequest $request, Product $product)
    {
        $this->authorize('out', Stock::class);

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
