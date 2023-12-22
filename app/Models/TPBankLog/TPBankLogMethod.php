<?php

namespace App\Models\TPBankLog;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait TPBankLogMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
