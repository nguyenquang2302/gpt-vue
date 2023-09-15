<?php

namespace App\Models\Branch;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait BranchMethod
{
    public function isActive(): bool
    {
        return true;
    }
   
}
