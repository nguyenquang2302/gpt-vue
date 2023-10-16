<?php

namespace App\Models\GlobalDetail;

/**
 * Class GlobalDetailScope.
 */
trait GlobalDetailScope
{
     /**
     * @param $query
     * @return mixed
     */
    public function scopeOnlyActive($query)
    {
        return $query->whereActive(true);
    }

}
