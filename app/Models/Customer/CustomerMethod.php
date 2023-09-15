<?php

namespace App\Models\Customer;

use Illuminate\Support\Collection;

/**
 * Trait UserMethod.
 */
trait CustomerMethod
{
    public function isActive(): bool
    {
        return $this->active;
    }

    public function getVipTotalMoney() 
    {
        if($this->vip_time){
            return number_format( $this->drawals()->where('created_at','>=',$this->vip_time->subDay(3))->sum('money_drawal') + $this->withdrawals()->where('created_at','>=',$this->vip_time->subDay(3))->sum('money_withdrawal'));

        } else {
            return number_format( $this->drawals()->sum('money_drawal') + $this->withdrawals()->sum('money_withdrawal'));
        }
    }
   
}
