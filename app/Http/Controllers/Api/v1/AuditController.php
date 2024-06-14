<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditCollection;
use App\Http\Resources\AuditResource;
use App\Models\Audit;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $audits = QueryBuilder::for(Audit::class)
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('product_id'),
            ])
            ->paginate($request->input('per_page', Audit::ITEMS_PER_PAGE));

        return new AuditCollection($audits);
    }
}
