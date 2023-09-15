<?php

namespace App\Models\Expense;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait ExpenseMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
