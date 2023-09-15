<?php

namespace App\Models\WithdrawalDetail;

use App\Domains\Auth\Models\User;
use App\Models\Bank\Bank;
use App\Models\Pos\Pos;
use App\Models\Withdrawal\Withdrawal;

/**
 * Class WithdrawalDetailRelationship.
 */
trait WithdrawalDetailRelationship
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
    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class);
    }
    public function pos()
    {
        return $this->belongsTo(Pos::class);
    }

    
}
