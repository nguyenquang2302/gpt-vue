<?php

namespace App\Models\FundCategory;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait FundCategoryMethod
{
    public function isActive()
    {
        return $this->active;
    }
   
}
