<?php

namespace App\Models\Withdrawal;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait WithdrawalMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function redundant() {
        $money = $this->withdrawalDetail()->sum('money');
        $money_drawal = $this->withdrawalDetail()->sum('money_drawal');
        if($money_drawal > $money) {
            $redundant = ($money_drawal - $money)* (1-$this->fee_customer/100);
            if($redundant > $this->fee_money_customer) {
                return 2;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
        
    }

    public function caclulatorProfit() {
        $fee_pos = $this->withDrawalDetail->sum('fee_money_bank');
        
        $money = $this->money_withdrawal;
        $money_drawal = $this->withDrawalDetail->sum('money_drawal');
        // if($money < $money_drawal) {
        //     $fee2 = ($money_drawal - $money) * $this->fee_customer/100;
        //     $fee_pos += $fee2;
        //     return $this->fee_money_customer-$fee_pos;
        // }
        // if($money < $money_drawal) {
        //     // số tiền rút dư
        //     $fee_customer_1 = ($money_drawal - $money);
        //     // phí cho số dư đó
        //     $fee_customer_2 = $fee_customer_1 * $this->fee_customer/100;
        //     // // 
        //     return $this->fee_money_customer + $fee_customer_2 - $fee_pos;
            
        //     // $fee2 = ($money_drawal - $money)  - ($money_drawal - $money) * $this->fee_customer/100;
        //     // dd($fee2);
        //     // dd($this->fee_money_customer + $fee2 - $fee_pos);

        // }
        return $this->fee_money_customer - $fee_pos;
    }
   
}
