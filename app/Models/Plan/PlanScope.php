<?php

namespace App\Models\Plan;

use Str;

/**
 * Class PlanScope.
 */
trait PlanScope
{
    /**
     * @param $query
     * @param $term
     * @return mixed
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', '%'.$term.'%')->orwhere('slug', 'like', '%'.Str::slug($term).'%');
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

    /**
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

}
