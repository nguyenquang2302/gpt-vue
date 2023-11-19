<?php

namespace App\Models\CustomerSchedule;

use App\Models\Customer\Customer;

/**
 * Class CustomerScheduleRelationship.
 */
trait CustomerScheduleRelationship
{
    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
}
