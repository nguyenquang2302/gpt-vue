<?php

namespace App\Models\Branch;

use App\Models\Customer\Customer;

/**
 * Class BranchRelationship.
 */
trait BranchRelationship
{
    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
}
