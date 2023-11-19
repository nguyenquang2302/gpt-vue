<?php

namespace App\Models\CustomerSchedule;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait CustomerScheduleMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
}
