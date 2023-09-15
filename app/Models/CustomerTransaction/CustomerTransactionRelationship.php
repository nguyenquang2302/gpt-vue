<?php

namespace App\Models\CustomerTransaction;

use App\Domains\Auth\Models\User;
use App\Models\Bank\Bank;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Drawal\Drawal;
use App\Models\Pos\Pos;

/**
 * Class CustomerTransactionRelationship.
 */
trait CustomerTransactionRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function drawal()
    {
        return $this->belongsTo(Drawal::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
