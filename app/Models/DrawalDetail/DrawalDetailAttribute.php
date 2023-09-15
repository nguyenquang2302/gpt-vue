<?php

namespace App\Models\DrawalDetail;

use Illuminate\Support\Facades\Hash;

/**
 * Trait DrawalDetailAttribute.
 */
trait DrawalDetailAttribute
{
    public function getGroupBillAttribute()
    {
        return $this->lo.'.'.$this->bill;
    }
   
}
