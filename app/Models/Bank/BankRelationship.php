<?php

namespace App\Models\Bank;

use App\Models\Customer\Customer;

/**
 * Class BankRelationship.
 */
trait BankRelationship
{
    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
}
