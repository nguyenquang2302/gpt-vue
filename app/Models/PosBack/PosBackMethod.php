<?php

namespace App\Models\PosBack;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait PosBackMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }
   
    public function getType() {
        return 'visa';
    }
}
