<?php

namespace App\Models\Pos;

use App\Models\Bank\Bank;
use App\Models\Drawal\Drawal;
use App\Models\DrawalDetail\DrawalDetail;
use App\Models\PosBack\PosBack;
use App\Models\PosConsignment\PosConsignment;
use App\Models\Users\User;
use App\Models\WithdrawalDetail\WithdrawalDetail;

/**
 * Class PosRelationship.
 */
trait PosRelationship
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
        return $this->hasMany(WithdrawalDetail::class);
    }

    public function posBack()
    {
        return $this->hasMany(PosBack::class);
    }

    public function drawalDetail()
    {
        return $this->hasMany(DrawalDetail::class);
    }

    public function posConsignment()
    {
        return $this->hasMany(PosConsignment::class);
    }
}
