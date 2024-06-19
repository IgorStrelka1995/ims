<?php

namespace Tests\Feature\Listeners;

use App\Events\ProductCreated;
use App\Models\Audit;
use App\Models\Product;
use App\Models\User;
use Database\Factories\ProductWithoutAuditFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class HandleCreatedProductToAuditTest extends TestCase
{
    use RefreshDatabase;

    public function testAddDataToAuditAfterProductCreated(): void
    {
        $product = ProductWithoutAuditFactory::new();

        $productData = $product->count(1)->create();

        $this->assertDatabaseCount('audit_logs', 0);

        event(new ProductCreated($productData->first()));

        $this->assertDatabaseCount('audit_logs', 1);

        $this->assertDatabaseHas('audit_logs', [
            'product_id' => $productData->first()->id,
            'action' => Audit::PRODUCT_ADD_ACTION
        ]);
    }
}
