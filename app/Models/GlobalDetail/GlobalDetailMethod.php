<?php

namespace App\Models\GlobalDetail;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait GlobalDetailMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
