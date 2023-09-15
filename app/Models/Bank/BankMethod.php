<?php

namespace App\Models\Bank;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait BankMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
