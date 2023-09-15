<?php

namespace App\Models\PosConsignment;

use Str;

/**
 * Class PosConsignmentScope.
 */
trait PosConsignmentScope
{
    /**
     * @param $query
     * @param $term
     * @return mixed
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('id', 'like', '%'.$term.'%')->orwhere('lo', 'like', '%'.Str::slug($term).'%');
        });
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOnlyDeactivated($query)
    {
        return $query->whereActive(false);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeOnlyActive($query)
    {
        return $query->whereActive(true);
    }


}
