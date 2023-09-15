<?php

namespace App\Models\Plan;

use App\Domains\Auth\Models\User;
use App\Models\Customer\Customer;

/**
 * Class PlanRelationship.
 */
trait PlanRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
