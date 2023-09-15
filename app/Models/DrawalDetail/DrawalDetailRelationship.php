<?php

namespace App\Models\DrawalDetail;

use App\Domains\Auth\Models\User;
use App\Models\Bank\Bank;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Drawal\Drawal;
use App\Models\Pos\Pos;

/**
 * Class DrawalDetailRelationship.
 */
trait DrawalDetailRelationship
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
    public function drawal()
    {
        return $this->belongsTo(Drawal::class);
    }
    public function pos()
    {
        return $this->belongsTo(Pos::class);
    }
    public function customerCard()
    {
        return $this->belongsTo(CustomerCard::class);
    }
    
}
