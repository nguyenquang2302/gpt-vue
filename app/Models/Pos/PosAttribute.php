<?php

namespace App\Models\Pos;

use App\Models\DrawalDetail\DrawalDetail;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Trait PosAttribute.
 */
trait PosAttribute
{
    public function getCaculatorLimitAttribute()
    {
        $money_drawal = DrawalDetail::whereDate('created_at', Carbon::today())->sum('money');
        $money_with_drawal = WithdrawalDetail::whereDate('created_at', Carbon::today())->sum('money');
        return $this->currency_limit - $money_drawal - $money_with_drawal;
    }

    public function getUrlQRAttribute() 
    {

        return 'https://img.vietqr.io/image/'.env('MB_POS_BACK_BANK_ID').'-'.env('MB_POS_ACCOUNT_ID').'-'.env('MB_POS_BACK_TEAMPLATE').'?addInfo='.$this->user->posName.'&accountName='.$this->user->name;
        
    }
   
}
