<?php

namespace App\Models\PosConsignment;

use App\Models\DrawalDetail\DrawalDetail;
use App\Models\PosBack\PosBack;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait PosConsignmentMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function getMoneyBack($active = 1, $from = NULL  , $to = NULL) {
        if($from && $to) {

        }
       return PosBack::where('pos_id',$this->pos_id)->where('lo',$this->lo)->sum('money');
    }

    public function getTotalMoney($active = 1, $from = NULL  , $to = NULL  ) {
        if($from && $to) {

            return ( DrawalDetail::where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('drawal',function (Builder $q1) use ($active, $from, $to) {
                $q1->where('isDone',$active)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);
            })
            ->sum('money_back') + 
            WithdrawalDetail::where('isOwnerPos',0)->where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('withdrawal',function (Builder $q1) use ($active, $from, $to) {
                $q1->where('isDone',$active)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);
            })
            ->sum('money_back')
            -
            WithdrawalDetail::where('isOwnerPos',1)->where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('withdrawal',function (Builder $q1) use ($active, $from, $to) {
                $q1->where('isDone',$active)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);
            })
            ->sum('fee_money_bank'));
            
        } else  {
            return ( DrawalDetail::where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('drawal',function (Builder $q1) use ($active) {
                $q1->where('isDone',$active);
            })
            ->sum('money_back') + 
            WithdrawalDetail::where('isOwnerPos',0)->where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('withdrawal',function (Builder $q1) use ($active) {
                $q1->where('isDone',$active);
            })
            ->sum('money_back')
            -
            WithdrawalDetail::where('isOwnerPos',1)->where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('withdrawal',function (Builder $q1) use ($active) {
                $q1->where('isDone',$active);
            })
            ->sum('fee_money_bank'));
        }

    }

    public function getTotal($active = 1, $from = NULL  , $to = NULL  ) {
        if($from && $to) {

            return ( DrawalDetail::where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('drawal',function (Builder $q1) use ($active, $from, $to) {
                $q1->where('isDone',$active)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);
            })
            ->sum('money_back') + WithdrawalDetail::where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('withdrawal',function (Builder $q1) use ($active, $from, $to) {
                $q1->where('isDone',$active)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);
            })
            ->sum('money_back'));
        } else  {
            return ( DrawalDetail::where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('drawal',function (Builder $q1) use ($active) {
                $q1->where('isDone',$active);
            })
            ->sum('money') + WithdrawalDetail::where('pos_id',$this->pos_id)->where('lo',$this->lo)
            ->whereHas('withdrawal',function (Builder $q1) use ($active) {
                $q1->where('isDone',$active);
            })
            ->sum('money_drawal'));
        }

    }

    public function getMoneyNotBack() {
        return PosBack::where('pos_id',$this->pos_id)->where('lo',$this->lo)->sum('money');
    }
   
}
