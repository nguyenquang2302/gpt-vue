<?php

namespace App\Models\Pos;

use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\PosConsignment\PosConsignment;
use App\Models\Withdrawal\Withdrawal;
use Google\Service\StreetViewPublish\Pose;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait PosMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function getAllMoneyWithdrawal($active, $from, $to)
    {
        if ($active) {
            $drawalDetails = $this->withDrawalDetail()->whereHas('withdrawal', function (Builder $query) use($from, $to) {
                $query->where('isDone', 1)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);

            })->get();
        } else {
            $drawalDetails = $this->drawalDetails;
        }
        $data['money'] = 0;
        $data['money_drawal'] = 0;
        $data['fee_bank_money'] = 0;
        $data['money_back'] = 0;
        $data['profit_money'] = 0 ;
        foreach ($drawalDetails??[] as $key => $drawalDetail) {
            if($drawalDetail->isOwnerPos) {
                $data['money'] +=  $drawalDetail->money;
                $data['money_drawal'] += $drawalDetail->money_drawal;
                $data['profit_money'] += - $drawalDetail->fee_money_bank;
                $data['fee_bank_money'] += $drawalDetail->fee_money_bank;
                $data['money_back'] -= $drawalDetail->fee_money_bank;
            } else {
                $data['money'] +=  $drawalDetail->money;
                $data['money_drawal'] += $drawalDetail->money_drawal;
                $data['profit_money'] += - $drawalDetail->fee_money_bank;
                $data['fee_bank_money'] += $drawalDetail->fee_money_bank;
                $data['money_back'] += $drawalDetail->money_back;
            }

        }
        return $data;
    }

    public function getAllMoneyDrawal($active, $from, $to)
    {
        if ($active) {
            $drawalDetails = $this->drawalDetail()->whereHas('drawal', function (Builder $query) use ($from, $to) {
                $query->where('isDone', 1)
                ->whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"]);
            })->get();
        } else {
            $drawalDetails = $this->drawalDetails;
        }
        $data['money'] = 0;
        $data['money_drawal'] = 0;
        $data['fee_bank_money'] = 0;
        $data['money_back'] = 0;
        $data['profit_money'] = 0 ;
        foreach ($drawalDetails??[] as $key => $drawalDetail) {
            if($drawalDetail->drawal->isDone){
                $customerTransaction = CustomerTransaction::where('source','CKRT')->where('key',$drawalDetail->drawal->id)->first();
                if($customerTransaction) {
                    $data['money_drawal']  -= $customerTransaction->money;
                    
                }
                $data['money'] +=  $drawalDetail->money;
                $data['profit_money'] +=  - $drawalDetail->fee_bank_money;
                $data['fee_bank_money'] += $drawalDetail->fee_bank_money;
                $data['money_drawal'] += $drawalDetail->money_drawal;
                $data['money_back'] += $drawalDetail->money_back;
            }
        }
        return $data;
    }

    public function getMoneyBack($active, $from, $to)
    {
        $moneyback = 0;
        $totalMoneyBack = 0;
        $posConsignments = $this->posConsignment()->get();
        foreach($posConsignments as $posConsignment) {
            $lo = $posConsignment->lo;
            $drawal_exits = Drawal::whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"])
            ->whereHas('drawalDetail',function (Builder $q1) use ($lo) {
                $q1->where('lo',$lo);
            })->first();
            
            $withdrawal_exits = Withdrawal::whereBetween('datetime', [$from->format('Y-m-d')." 00:00:00", $to->format('Y-m-d')." 23:59:59"])
            ->whereHas('withdrawalDetail',function (Builder $q1) use ($lo) {
                $q1->where('lo',$lo);
            })
            ->count();
            if($drawal_exits || $withdrawal_exits) {

                $moneyback += $posConsignment->getMoneyBack($active, $from, $to);
                $totalMoneyBack += $posConsignment->getTotalMoney($active, $from, $to);
                if((int)$moneyback != (int)$totalMoneyBack) {
                // var_dump($posConsignment->getMoneyBack($active, $from, $to),$posConsignment->getTotalMoney($active, $from, $to),$posConsignment->id);
                }

            }

        }
        
        $data['moneyback'] = $moneyback;
        $data['totalMoneyBack'] = $totalMoneyBack;
        $data['money_not_back_yet'] = $totalMoneyBack - $moneyback;
        return $data;
    }
    

    public function getTotalMoneyBack()
    {
        $moneyback = 0;
        $totalMoneyBack = 0;
        $posConsignments = $this->posConsignment()->get();
        foreach($posConsignments as $posConsignment) {
            $lo = $posConsignment->lo;
            $drawal_exits = Drawal::whereHas('drawalDetail',function (Builder $q1) use ($lo) {
                $q1->where('lo',$lo);
            })->first();
            
            $withdrawal_exits = Withdrawal::whereHas('withdrawalDetail',function (Builder $q1) use ($lo) {
                $q1->where('lo',$lo);
            })
            ->count();
            if($drawal_exits || $withdrawal_exits) {

                $moneyback += $posConsignment->getMoneyBack();
                $totalMoneyBack += $posConsignment->getTotalMoney();
            }

        }
        
        $data['moneyback'] = $moneyback;
        $data['totalMoneyBack'] = $totalMoneyBack;
        $data['money_not_back_yet'] = $totalMoneyBack - $moneyback;
        return $data;
    }


}
