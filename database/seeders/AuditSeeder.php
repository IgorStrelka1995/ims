<?php

namespace Database\Seeders;

use App\Models\Audit;
use Database\Factories\AuditProductStockFactory;
use Database\Factories\AuditProductUpdateFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $auditProductUpdateFactory = new AuditProductUpdateFactory();
        $auditProductUpdateFactory->count(300)->create();

        $auditProductStockFactory = new AuditProductStockFactory();
        $auditProductStockFactory->count(300)->create();
    }
}
