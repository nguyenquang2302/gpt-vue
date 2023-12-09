<?php

namespace App\Models\Users\Traits\Relationship;

use App\Models\Users\PasswordHistory;
use App\Models\Branch\Branch;
use App\Models\FundTransaction\FundTransaction;
use App\Models\Pos\Pos;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * @return mixed
     */
    public function passwordHistories()
    {
        return $this->morphMany(PasswordHistory::class, 'model');
    }

    public function pos()
    {
        return $this->hasMany(Pos::class,'user_id_belongto','id');
    }

    public function branchs()
    {
        return $this->belongsToMany(Branch::class,'users_branchs');
    }
    public function branch()
    {
        return Branch::where('id',$this->branch_id)->first();
    }


    public function fundTransaction() {
        return $this->hasMany(FundTransaction::class);
    }
    
}
