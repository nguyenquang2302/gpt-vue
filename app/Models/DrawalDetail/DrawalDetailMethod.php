<?php

namespace App\Models\DrawalDetail;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait DrawalDetailMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
