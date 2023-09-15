<?php

namespace App\Models\Expense;

use App\Domains\Auth\Models\User;
use App\Models\Bank\Bank;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Drawal\Drawal;
use App\Models\FundCategory\FundCategory;
use App\Models\Pos\Pos;

/**
 * Class ExpenseRelationship.
 */
trait ExpenseRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
   

    public function fundCategory()
    {
        return $this->belongsTo(FundCategory::class);
    }

}
