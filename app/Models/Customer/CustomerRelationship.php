<?php

namespace App\Models\Customer;

use App\Models\Branch\Branch;
use App\Models\CustomerSchedule\CustomerSchedule;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\Users\User;
use App\Models\Withdrawal\Withdrawal;
use HoangPhi\VietnamMap\Models\Province;
use HoangPhi\VietnamMap\Models\District;
use HoangPhi\VietnamMap\Models\Ward;

/**
 * Class CustomerRelationship.
 */
trait CustomerRelationship
{
    /**
     * @return mixed
     */
    public function province()
    {
        return $this->belongsTo(Province::class);
    }
    
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function drawals()
    {
        return $this->hasMany(Drawal::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function customerTransactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }


    public function schedule()
    {
        return $this->hasMany(CustomerSchedule::class);
    }

}
