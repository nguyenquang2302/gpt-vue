<?php

namespace App\Models\PosBack;

use App\Models\Customer\Customer;
use App\Models\Pos\Pos;

/**
 * Class PosBackRelationship.
 */
trait PosBackRelationship
{
    /**
     * @return mixed
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function pos()
    {
        return $this->belongsTo(Pos::class);
    }
    
}
