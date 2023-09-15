<?php

namespace App\Models\CustomerTransaction;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait CustomerTransactionMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
