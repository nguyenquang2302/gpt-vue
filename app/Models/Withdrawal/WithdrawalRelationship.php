<?php

namespace App\Models\Withdrawal;

use App\Domains\Auth\Models\User;
use App\Models\Bank\Bank;
use App\Models\Branch\Branch;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\WithdrawalDetail\WithdrawalDetail;

/**
 * Class WithdrawalRelationship.
 */
trait WithdrawalRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userBelongto()
    {
        return $this->belongsTo(User::class, 'user_id_belongto', 'id');
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function withdrawalDetail()
    {
        return $this->hasMany(WithdrawalDetail::class,'withdrawal_id','id');
    }
    public function customerCard()
    {
        return $this->belongsTo(CustomerCard::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
