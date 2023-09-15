<?php

namespace App\Models\Drawal;

use App\Domains\Auth\Models\User;
use App\Models\Bank\Bank;
use App\Models\Branch\Branch;
use App\Models\Customer\Customer;
use App\Models\DrawalDetail\DrawalDetail;
use Google\Service\Monitoring\Custom;

/**
 * Class DrawalRelationship.
 */
trait DrawalRelationship
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function drawalDetail()
    {
        return $this->hasMany(DrawalDetail::class,'drawal_id','id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
