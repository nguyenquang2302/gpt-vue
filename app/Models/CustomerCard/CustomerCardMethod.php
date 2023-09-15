<?php

namespace App\Models\CustomerCard;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait CustomerCardMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
    public function getType() {
        return 'visa';
    }
}
