<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditCollection;
use App\Models\Audit;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuditController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/audits",
     *     summary="List of all audit logs",
     *     tags={"Audit"},
     *     security={{"bearerAuth": ""}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="product_id", type="integer"),
     *                  @OA\Property(property="action", type="string"),
     *                  @OA\Property(property="created_at", type="string")
     *              )),
     *              example={
     *                  "data": {{
     *                      "id": 1,
     *                      "product_id": 12,
     *                      "action": "product_add",
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  },
     *                  {
     *                      "id": 2,
     *                      "product_id": 13,
     *                      "action": "product_add",
     *                      "created_at": "2024-06-26T07:51:10.000000Z"
     *                  },}
     *              }
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return AuditCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Audit::class);

        $audits = QueryBuilder::for(Audit::class)
            ->allowedFilters([AllowedFilter::exact('product_id')])
            ->paginate($request->input('per_page', Audit::ITEMS_PER_PAGE))
        ;

        return new AuditCollection($audits);
    }
}
