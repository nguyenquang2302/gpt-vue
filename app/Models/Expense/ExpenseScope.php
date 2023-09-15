<?php

namespace App\Models\Expense;

/**
 * Class ExpenseScope.
 */
trait ExpenseScope
{
     /**
     * @param $query
     * @return mixed
     */
    public function scopeOnlyActive($query)
    {
        return $query->whereActive(true);
    }

}
