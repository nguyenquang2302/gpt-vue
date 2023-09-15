<?php

namespace App\Models\Drawal;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait DrawalMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function getMoneyTransfer() {
        if($this->transfer) {
            return $this->money_drawal - ($this->money_drawal*$this->fee_customer/100) - $this->fee_ship;
        }else {
            return $this->money_drawal;
        }
    }

    public function caclulatorProfit() {
        $fee_pos = $this->drawalDetail->sum('fee_bank_money');
        return $this->fee_money_customer - $fee_pos;
    }
    
   
}
