<?php

namespace App\Models\FundTransaction;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait FundTransactionMethod
{
    public function isActive()
    {
        return $this->active;
    }
   
}
