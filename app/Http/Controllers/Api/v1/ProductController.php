<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="List of all products",
     *     tags={"Product"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="sku", type="string"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="price", type="integer"),
     *                  @OA\Property(property="stock", type="integer"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *              )),
     *              example={
     *                  "data": {{
     *                      "id": 1,
     *                      "sku": "sit-consequuntur-rerum",
     *                      "name": "sit consequuntur rerum",
     *                      "price": "490.66",
     *                      "stock": 99,
     *                      "description": "Fugiat eum et ut. Autem totam sunt asperiores minima.",
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  },
     *                  {
     *                      "id": 2,
     *                      "sku": "aut-omnis-vitae",
     *                      "name": "aut omnis vitae",
     *                      "price": "604.06",
     *                      "stock": 78,
     *                      "description": "Deserunt sed nobis est sint minus. Voluptas ut ut dolores laudantium et tempora.",
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  }}
     *              }
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return ProductCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
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

        return new ProductCollection($products);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create new product.",
     *     tags={"Product"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                      @OA\Property(property="sku", type="string"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string"),
     *                      @OA\Property(property="price", type="string"),
     *                      @OA\Property(property="stock", type="integer"),
     *                 )
     *             },
     *              example={
     *                  "sku": "lorem-ipsum",
     *                  "name": "Lorem Ipsum",
     *                  "description": "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
     *                  "price": "10.20",
     *                  "stock": 10
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
     *                  @OA\Property(property="sku", type="string"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="price", type="integer"),
     *                  @OA\Property(property="stock", type="integer"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="created_at", type="string"),
     *              )
     *         )
     *     )
     * )
     * @param StoreProductRequest $request
     * @return ProductResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * @OA\Get(
     *     path="/api/v1/products/{product}",
     *     summary="Get single product",
     *     tags={"Product"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Product id",
     *          in="path",
     *          name="product",
     *          required=true,
     *          example=1
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="sku", type="string", example="lorem-ipsum"),
     *                  @OA\Property(property="name", type="string", example="name"),
     *                  @OA\Property(property="price", type="integer", example="10.20"),
     *                  @OA\Property(property="stock", type="integer", example=20),
     *                  @OA\Property(property="description", type="string", example="Voluptatum sequi odio sint dolorem consectetur nihil quasi."),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z")
     *              )
     *         )
     *     )
     * )
     *
     * @param Product $product
     * @return ProductResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Product $product)
    {
        $this->authorize('view', Product::class);

        return ProductResource::make($product);
    }

    /**
     *  @OA\Put(
     *     path="/api/v1/products/{product}",
     *     summary="Update product",
     *     tags={"Product"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
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
     *                      @OA\Property(property="sku", type="string"),
     *                      @OA\Property(property="name", type="string"),
     *                      @OA\Property(property="description", type="string"),
     *                      @OA\Property(property="price", type="string"),
     *                      @OA\Property(property="stock", type="integer"),
     *                 )
     *             },
     *              example={
     *                  "sku": "lorem-ipsum-1111",
     *                  "name": "Lorem Ipsum",
     *                  "description": "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
     *                  "price": "10.20",
     *                  "stock": 10
     *              }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="sku", type="string", example="lorem-ipsum"),
     *                  @OA\Property(property="name", type="string", example="name"),
     *                  @OA\Property(property="price", type="integer", example="10.20"),
     *                  @OA\Property(property="stock", type="integer", example=20),
     *                  @OA\Property(property="description", type="string", example="Voluptatum sequi odio sint dolorem consectetur nihil quasi."),
     *                  @OA\Property(property="created_at", type="string", example="2024-06-26T07:51:10.000000Z")
     *              )
     *         )
     *     )
     * )
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return ProductResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     summary="Delete product",
     *     tags={"Product"},
     *     security={{"bearerAuth": ""}},
     *
     *      @OA\Parameter(
     *          description="Product id",
     *          in="path",
     *          name="product",
     *          required=true,
     *          example=1
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="OK"
     *     )
     * )
     *
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', Product::class);

        $product->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
