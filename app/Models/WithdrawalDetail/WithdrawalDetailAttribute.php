<?php

namespace App\Models\WithdrawalDetail;

use Illuminate\Support\Facades\Hash;

/**
 * Trait WithdrawalDetailAttribute.
 */
trait WithdrawalDetailAttribute
{
    public function getGroupBillAttribute()
    {
        return $this->lo.'.'.$this->bill;
    }
}
