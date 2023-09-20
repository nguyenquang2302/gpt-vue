<?php

namespace App\Models\PartnerTransaction;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait PartnerTransactionMethod
{
    public function isActive()
    {
        return $this->active;
    }
   
}
