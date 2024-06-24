<?php

namespace Tests;

use App\Models\Audit;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setupRolesToPermissions()
    {
        $permissions = [
            Product::PRODUCT_STORE_PERMISSION, Product::PRODUCT_VIEW_PERMISSION, Product::PRODUCT_UPDATE_PERMISSION,
            Product::PRODUCT_DESTROY_PERMISSION, Stock::STOCK_VIEW_PERMISSION, Stock::STOCK_IN_PERMISSION,
            Stock::STOCK_OUT_PERMISSION, Audit::AUDIT_VIEW_PERMISSION, User::USER_VIEW_PERMISSION,
            User::USER_CREATE_PERMISSION, User::USER_UPDATE_PERMISSION, User::USER_DELETE_PERMISSION
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $roles = [
            User::ROLE_ADMIN => [
                Product::PRODUCT_STORE_PERMISSION, Product::PRODUCT_VIEW_PERMISSION, Product::PRODUCT_UPDATE_PERMISSION,
                Product::PRODUCT_DESTROY_PERMISSION, Stock::STOCK_VIEW_PERMISSION, Stock::STOCK_IN_PERMISSION,
                Stock::STOCK_OUT_PERMISSION, Audit::AUDIT_VIEW_PERMISSION, User::USER_VIEW_PERMISSION,
                User::USER_CREATE_PERMISSION, User::USER_UPDATE_PERMISSION, User::USER_DELETE_PERMISSION
            ],
            User::ROLE_INVENTORY_MANAGER => [
                Product::PRODUCT_VIEW_PERMISSION, Stock::STOCK_VIEW_PERMISSION, Stock::STOCK_IN_PERMISSION,
                Stock::STOCK_OUT_PERMISSION, Audit::AUDIT_VIEW_PERMISSION
            ],
            User::ROLE_VIEWER => [
                Product::PRODUCT_VIEW_PERMISSION, Stock::STOCK_VIEW_PERMISSION, Audit::AUDIT_VIEW_PERMISSION
            ]
        ];

        foreach ($roles as $role => $rolePermissions) {
            $role = Role::create(['name' => $role]);

            $role->givePermissionTo($rolePermissions);
        }
    }
}
