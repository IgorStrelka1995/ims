<?php

namespace App\Policies;

use App\Models\Stock;
use App\Models\User;

class StockPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return ($user->can(Stock::STOCK_VIEW_PERMISSION));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return ($user->can(Stock::STOCK_VIEW_PERMISSION));
    }

    /**
     * Determine whether the user can create models.
     */
    public function in(User $user): bool
    {
        return ($user->can(Stock::STOCK_IN_PERMISSION));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function out(User $user): bool
    {
        return ($user->can(Stock::STOCK_OUT_PERMISSION));
    }
}
