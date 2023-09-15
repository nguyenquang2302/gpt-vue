<?php

namespace App\Models\Plan;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait PlanMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
