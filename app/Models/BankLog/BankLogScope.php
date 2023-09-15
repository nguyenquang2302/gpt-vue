<?php

namespace App\Models\BankLog;

use Carbon\Carbon;
use Prophecy\Call\Call;

/**
 * Class BankLogScope.
 */
trait BankLogScope
{
    /**
     * @param $query
     * @param $term
     * @return mixed
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('refNo', 'like', '%'.$term.'%')
            ->orWhere('creditAmount', 'like', '%'.$term.'%')
            ->orWhere('debitAmount', 'like', '%'.$term.'%')
            ->orWhere('description', 'like', '%'.$term.'%')
            ->orWhere('content', 'like', '%'.$term.'%')
            ->orWhere('content_fix', 'like', '%'.$term.'%');
        });
    }

    public function scopeInday($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            if($term ="yes") {
                $query->whereDate('created_at', Carbon::today());
            }

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

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAllAccess($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', config('boilerplate.access.role.admin'));
        });
    }

}
