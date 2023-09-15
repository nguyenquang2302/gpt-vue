<?php

namespace App\Models\Drawal;

use Illuminate\Support\Facades\Hash;

/**
 * Trait DrawalAttribute.
 */
trait DrawalAttribute
{
   
    
    public function setMoneyAttribute($value)
    {
        $this->attributes['money'] =  ceil($value/1000)*1000;
    }
    
}
