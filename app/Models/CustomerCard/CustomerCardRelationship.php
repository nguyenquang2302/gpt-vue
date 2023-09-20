<?php

namespace App\Models\CustomerCard;

use App\Models\Bank\Bank;
use App\Models\Branch\Branch;
use App\Models\Customer\Customer;
use App\Models\Users\User;

/**
 * Class CustomerCardRelationship.
 */
trait CustomerCardRelationship
{
    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
}
