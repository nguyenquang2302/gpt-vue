<?php

namespace App\Models\FundTransaction;

use App\Models\Customer\Customer;
use App\Models\FundCategory\FundCategory;

/**
 * Class FundTransactionRelationship.
 */
trait FundTransactionRelationship
{
    public function fundCategory()
    {
        return $this->belongsTo(FundCategory::class);
    }
}
