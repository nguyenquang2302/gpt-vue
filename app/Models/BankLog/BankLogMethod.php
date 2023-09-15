<?php

namespace App\Models\BankLog;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait BankLogMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
