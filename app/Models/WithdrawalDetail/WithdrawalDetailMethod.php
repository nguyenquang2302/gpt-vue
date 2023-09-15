<?php

namespace App\Models\WithdrawalDetail;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait WithdrawalDetailMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
